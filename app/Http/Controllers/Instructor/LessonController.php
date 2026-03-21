<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instructor;

use App\Domain\Content\Actions\CreateLesson;
use App\Domain\Content\Actions\UpdateLesson;
use App\Domain\Content\Models\Lesson;
use App\Domain\Content\Models\Module;
use App\Domain\Course\Models\CourseSection;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lesson\CreateLessonRequest;
use App\Http\Requests\Lesson\UpdateLessonRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LessonController extends Controller
{
    public function create(Request $request, CourseSection $section, Module $module): Response
    {
        $this->authorize('create', Lesson::class);

        return Inertia::render('Instructor/Lessons/Create', [
            'section' => $section,
            'module' => $module,
        ]);
    }

    public function store(CreateLessonRequest $request, CourseSection $section, Module $module, CreateLesson $action): RedirectResponse
    {
        $this->authorize('create', Lesson::class);

        $action->execute($module->id, $request->validated());

        return redirect()
            ->route('instructor.courses.modules.index', $section)
            ->with('success', 'Lesson created.');
    }

    public function edit(Request $request, CourseSection $section, Module $module, Lesson $lesson): Response
    {
        $this->authorize('update', $lesson);

        return Inertia::render('Instructor/Lessons/Edit', [
            'section' => $section,
            'module' => $module,
            'lesson' => $lesson,
        ]);
    }

    public function update(UpdateLessonRequest $request, CourseSection $section, Module $module, Lesson $lesson, UpdateLesson $action): RedirectResponse
    {
        $this->authorize('update', $lesson);

        $action->execute($lesson, $request->validated());

        return redirect()
            ->route('instructor.courses.modules.index', $section)
            ->with('success', 'Lesson updated.');
    }

    public function destroy(Request $request, CourseSection $section, Module $module, Lesson $lesson): RedirectResponse
    {
        $this->authorize('delete', $lesson);

        $lesson->delete();

        return redirect()
            ->route('instructor.courses.modules.index', $section)
            ->with('success', 'Lesson deleted.');
    }
}
