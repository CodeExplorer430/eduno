<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Course\Actions\CreateCourseSection;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Http\Requests\StoreCourseSectionRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseSectionController extends Controller
{
    public function store(StoreCourseSectionRequest $request, Course $course, CreateCourseSection $action): RedirectResponse
    {
        $this->authorize('create', [CourseSection::class, $course]);

        $data = $request->validated();

        /** @var User $instructor */
        $instructor = User::findOrFail($data['instructor_id']);

        $action->handle($course, $instructor, $data);

        return redirect()->route('courses.show', $course)
            ->with('success', 'Section created successfully.');
    }

    public function update(Request $request, CourseSection $section): RedirectResponse
    {
        $this->authorize('update', $section);

        $validated = $request->validate([
            'section_name' => ['required', 'string', 'max:255'],
            'schedule_text' => ['nullable', 'string', 'max:255'],
        ]);

        $section->update($validated);

        return redirect()->route('courses.show', $section->course_id)
            ->with('success', 'Section updated successfully.');
    }

    public function destroy(CourseSection $section): RedirectResponse
    {
        $this->authorize('delete', $section);

        $courseId = $section->course_id;
        $section->delete();

        return redirect()->route('courses.show', $courseId)
            ->with('success', 'Section deleted.');
    }
}
