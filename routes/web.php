<?php

use App\Http\Controllers\AccessibilityController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\AnnouncementController;
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

    // Accessibility preferences
    Route::get('preferences', [PreferencesController::class, 'edit'])->name('preferences.edit');
    Route::put('preferences', [PreferencesController::class, 'update'])->name('preferences.update');

    // Admin reports
    Route::get('admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
});

require __DIR__.'/auth.php';
