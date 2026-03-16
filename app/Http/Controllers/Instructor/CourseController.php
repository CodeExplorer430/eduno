<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instructor;

use App\Domain\Course\Actions\CreateCourse;
use App\Domain\Course\Actions\UpdateCourse;
use App\Domain\Course\Models\Course;
use App\Http\Controllers\Controller;
use App\Http\Requests\Course\CreateCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    public function __construct(
        private readonly CreateCourse $createCourse,
        private readonly UpdateCourse $updateCourse,
    ) {}

    public function index(Request $request): Response
    {
        abort_unless($request->user()->isInstructor() || $request->user()->isAdmin(), 403);
        $this->authorize('viewAny', Course::class);

        $courses = Course::where('created_by', $request->user()->id)
            ->with('sections')
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Instructor/Courses/Index', [
            'courses' => $courses,
        ]);
    }

    public function create(Request $request): Response
    {
        abort_unless($request->user()->isInstructor() || $request->user()->isAdmin(), 403);
        $this->authorize('create', Course::class);

        return Inertia::render('Instructor/Courses/Create');
    }

    public function store(CreateCourseRequest $request): RedirectResponse
    {
        abort_unless($request->user()->isInstructor() || $request->user()->isAdmin(), 403);
        $this->authorize('create', Course::class);

        $this->createCourse->execute($request->user(), $request->validated());

        return redirect()->route('instructor.courses.index')
            ->with('success', 'Course created successfully.');
    }

    public function edit(Request $request, Course $course): Response
    {
        abort_unless($request->user()->isInstructor() || $request->user()->isAdmin(), 403);
        $this->authorize('update', $course);

        return Inertia::render('Instructor/Courses/Edit', [
            'course' => $course,
        ]);
    }

    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        abort_unless($request->user()->isInstructor() || $request->user()->isAdmin(), 403);
        $this->authorize('update', $course);

        $this->updateCourse->execute($request->user(), $course, $request->validated());

        return redirect()->route('instructor.courses.index')
            ->with('success', 'Course updated successfully.');
    }
}
