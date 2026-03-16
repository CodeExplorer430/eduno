<?php

use App\Http\Controllers\Admin;
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

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
