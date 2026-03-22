<?php

use App\Http\Controllers\AccessibilityController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Profile\AccessibilityController as ProfileAccessibilityController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Instructor;
use App\Http\Controllers\Student;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseSectionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\PreferencesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\SubmissionExportController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::patch('/profile/preferences', [AccessibilityController::class, 'update'])
        ->name('profile.preferences');

    Route::get('/profile/accessibility', [ProfileAccessibilityController::class, 'edit'])
        ->name('profile.accessibility.edit');
    Route::patch('/profile/accessibility', [ProfileAccessibilityController::class, 'update'])
        ->name('profile.accessibility.update');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('courses', CourseController::class);
    Route::resource('courses.sections', CourseSectionController::class)->shallow();
    Route::post('sections/{section}/enroll', [EnrollmentController::class, 'store'])->name('sections.enroll');
    Route::delete('sections/{section}/enroll', [EnrollmentController::class, 'destroy'])->name('sections.unenroll');

    // Modules (nested under sections, shallow)
    Route::resource('sections.modules', ModuleController::class)->shallow();
    Route::post('modules/{module}/publish', [ModuleController::class, 'publish'])->name('modules.publish');

    // Lessons (nested under modules, shallow, no index)
    Route::resource('modules.lessons', LessonController::class)->shallow()->except(['index']);
    Route::post('lessons/{lesson}/publish', [LessonController::class, 'publish'])->name('lessons.publish');

    // Resources
    Route::post('lessons/{lesson}/resources', [ResourceController::class, 'store'])->name('lessons.resources.store');
    Route::delete('resources/{resource}', [ResourceController::class, 'destroy'])->name('resources.destroy');
    Route::get('resources/{resource}/download', [ResourceController::class, 'show'])->name('resources.download');

    // Announcements (nested under sections, shallow)
    Route::resource('sections.announcements', AnnouncementController::class)->shallow();
    Route::post('announcements/{announcement}/publish', [AnnouncementController::class, 'publish'])
        ->name('announcements.publish');

    // Assignments (nested under sections, shallow)
    Route::resource('sections.assignments', AssignmentController::class)->shallow();
    Route::post('assignments/{assignment}/publish', [AssignmentController::class, 'publish'])
        ->name('assignments.publish');

    // Submissions
    Route::get('assignments/{assignment}/submissions', [SubmissionController::class, 'index'])
        ->name('assignments.submissions.index');
    Route::post('assignments/{assignment}/submissions', [SubmissionController::class, 'store'])
        ->name('assignments.submissions.store');
    Route::get('submissions/{submission}', [SubmissionController::class, 'show'])
        ->name('submissions.show');
    Route::delete('submissions/{submission}', [SubmissionController::class, 'destroy'])
        ->name('submissions.destroy');

    // Grading
    Route::post('submissions/{submission}/grade', [GradeController::class, 'store'])
        ->name('submissions.grade.store');
    Route::patch('grades/{grade}', [GradeController::class, 'update'])
        ->name('grades.update');
    Route::post('grades/{grade}/release', [GradeController::class, 'release'])
        ->name('grades.release');

    // Submission export
    Route::get('assignments/{assignment}/submissions/export', SubmissionExportController::class)
        ->name('assignments.submissions.export');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Accessibility preferences
    Route::get('preferences', [PreferencesController::class, 'edit'])->name('preferences.edit');
    Route::put('preferences', [PreferencesController::class, 'update'])->name('preferences.update');

    // Admin reports
    Route::get('admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
});

// Role-namespaced routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Student routes
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/courses', [Student\CourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/{section}', [Student\CourseController::class, 'show'])->name('courses.show');
        Route::get('/assignments', [Student\AssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/{assignment}', [Student\AssignmentController::class, 'show'])->name('assignments.show');
        Route::get('/assignments/{assignment}/submit', [Student\SubmissionController::class, 'create'])->name('submissions.create');
        Route::post('/assignments/{assignment}/submit', [Student\SubmissionController::class, 'store'])->name('submissions.store');
        Route::get('/submissions/{submission}', [Student\SubmissionController::class, 'show'])->name('submissions.show');
        Route::get('/grades', [Student\GradeController::class, 'index'])->name('grades.index');
        Route::get('/announcements', [Student\AnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('/lessons/{lesson}', [Student\LessonController::class, 'show'])->name('lessons.show');
        Route::get('/resources/{resource}/download', [Student\ResourceController::class, 'download'])->name('resources.download');
    });

    // Instructor routes
    Route::prefix('instructor')->name('instructor.')->group(function () {
        Route::resource('courses', Instructor\CourseController::class)
            ->only(['index', 'create', 'store', 'edit', 'update']);
        Route::resource('courses.assignments', Instructor\AssignmentController::class)
            ->shallow()
            ->parameters(['courses' => 'section']);
        Route::get('/assignments/{assignment}/submissions', [Instructor\SubmissionController::class, 'index'])
            ->name('submissions.index');
        Route::get('/assignments/{assignment}/submissions/export', [Instructor\SubmissionController::class, 'export'])
            ->name('submissions.export');
        Route::get('/submissions/{submission}', [Instructor\SubmissionController::class, 'show'])
            ->name('submissions.show');
        Route::post('/submissions/{submission}/grade', [Instructor\GradeController::class, 'store'])
            ->name('grades.store');
        Route::patch('/grades/{grade}/release', [Instructor\GradeController::class, 'release'])
            ->name('grades.release');
        Route::resource('announcements', Instructor\AnnouncementController::class)
            ->except(['show']);

        Route::prefix('courses/{section}/modules')->name('courses.modules.')->group(function () {
            Route::get('/', [Instructor\ModuleController::class, 'index'])->name('index');
            Route::get('/create', [Instructor\ModuleController::class, 'create'])->name('create');
            Route::post('/', [Instructor\ModuleController::class, 'store'])->name('store');
            Route::get('/{module}/edit', [Instructor\ModuleController::class, 'edit'])->name('edit');
            Route::patch('/{module}', [Instructor\ModuleController::class, 'update'])->name('update');
            Route::delete('/{module}', [Instructor\ModuleController::class, 'destroy'])->name('destroy');

            Route::prefix('{module}/lessons')->name('lessons.')->group(function () {
                Route::get('/create', [Instructor\LessonController::class, 'create'])->name('create');
                Route::post('/', [Instructor\LessonController::class, 'store'])->name('store');
                Route::get('/{lesson}/edit', [Instructor\LessonController::class, 'edit'])->name('edit');
                Route::patch('/{lesson}', [Instructor\LessonController::class, 'update'])->name('update');
                Route::delete('/{lesson}', [Instructor\LessonController::class, 'destroy'])->name('destroy');

                Route::prefix('{lesson}/resources')->name('resources.')->group(function () {
                    Route::get('/upload', [Instructor\ResourceController::class, 'create'])->name('create');
                    Route::post('/', [Instructor\ResourceController::class, 'store'])->name('store');
                    Route::delete('/{resource}', [Instructor\ResourceController::class, 'destroy'])->name('destroy');
                });
            });
        });
    });

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', Admin\UserController::class)
            ->only(['index', 'edit', 'update']);
        Route::get('/courses', [Admin\CourseController::class, 'index'])->name('courses.index');
        Route::patch('/courses/{course}/status', [Admin\CourseController::class, 'updateStatus'])->name('courses.updateStatus');
        Route::get('/audit-logs', [Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [Admin\ReportController::class, 'export'])->name('reports.export');
    });
});

require __DIR__.'/auth.php';
