<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Course\Actions\CreateCourse;
use App\Domain\Course\Actions\UpdateCourse;
use App\Domain\Course\Models\Course;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Course::class);

        /** @var User $user */
        $user = auth()->user();

        $courses = $user->isStudent()
            ? Course::whereHas(
                'sections.enrollments',
                fn ($q) => $q->where('user_id', $user->id)->where('status', 'active')
            )
                ->with(['creator', 'sections'])
                ->paginate(15)
            : Course::where('created_by', $user->id)
                ->with(['creator', 'sections'])
                ->paginate(15);

        return Inertia::render('Course/Index', [
            'courses' => $courses,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Course::class);

        return Inertia::render('Course/Create');
    }

    public function store(StoreCourseRequest $request, CreateCourse $action): RedirectResponse
    {
        $this->authorize('create', Course::class);

        $course = $action->handle($request->user(), $request->validated());

        return redirect()->route('courses.show', $course)
            ->with('success', 'Course created successfully.');
    }

    public function show(Course $course): Response
    {
        $this->authorize('view', $course);

        $course->load([
            'creator',
            'sections.instructor',
            'sections.enrollments' => fn ($q) => $q->select(['id', 'user_id', 'course_section_id']),
        ]);

        return Inertia::render('Course/Show', [
            'course' => $course,
        ]);
    }

    public function edit(Course $course): Response
    {
        $this->authorize('update', $course);

        return Inertia::render('Course/Edit', [
            'course' => $course,
        ]);
    }

    public function update(UpdateCourseRequest $request, Course $course, UpdateCourse $action): RedirectResponse
    {
        $this->authorize('update', $course);

        /** @var User $user */
        $user = $request->user();

        $action->handle($course, $request->validated(), $user);

        return redirect()->route('courses.show', $course)
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $this->authorize('delete', $course);

        $course->delete();

        return redirect()->route('courses.index')
            ->with('success', 'Course deleted.');
    }
}
