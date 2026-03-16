<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Instructor;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

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
            ->shallow();
        Route::get('/assignments/{assignment}/submissions', [Instructor\SubmissionController::class, 'index'])
            ->name('submissions.index');
        Route::get('/submissions/{submission}', [Instructor\SubmissionController::class, 'show'])
            ->name('submissions.show');
        Route::post('/submissions/{submission}/grade', [Instructor\GradeController::class, 'store'])
            ->name('grades.store');
        Route::patch('/grades/{grade}/release', [Instructor\GradeController::class, 'release'])
            ->name('grades.release');
        Route::resource('announcements', Instructor\AnnouncementController::class)
            ->except(['show']);

        // Module / Lesson / Resource management
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
        Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [Admin\ReportController::class, 'export'])->name('reports.export');
    });
});

require __DIR__.'/auth.php';
