<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseSectionController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResourceController;
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
});

require __DIR__.'/auth.php';
