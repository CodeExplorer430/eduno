<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Module\Actions\CreateLesson;
use App\Domain\Module\Actions\PublishLesson;
use App\Domain\Module\Actions\UpdateLesson;
use App\Domain\Module\Models\Lesson;
use App\Domain\Module\Models\Module;
use App\Enums\ResourceVisibility;
use App\Http\Requests\Module\StoreLessonRequest;
use App\Http\Requests\Module\UpdateLessonRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LessonController extends Controller
{
    public function create(Module $module): Response
    {
        $this->authorize('create', [Lesson::class, $module]);

        $module->load('section.course');

        return Inertia::render('Lesson/Create', [
            'module' => $module,
        ]);
    }

    public function store(StoreLessonRequest $request, Module $module, CreateLesson $action): RedirectResponse
    {
        $this->authorize('create', [Lesson::class, $module]);

        $action->handle($module, $request->validated());

        return redirect()->route('modules.show', $module)
            ->with('success', 'Lesson created successfully.');
    }

    public function show(Lesson $lesson): Response
    {
        $this->authorize('view', $lesson);

        /** @var User $user */
        $user = auth()->user();

        $lesson->load('module.section.course');

        $query = $lesson->resources()->orderBy('title');

        if ($user->isStudent()) {
            $query->whereIn('visibility', [
                ResourceVisibility::Enrolled->value,
                ResourceVisibility::Public->value,
            ]);
        } elseif (! $user->isAdmin()) {
            $query->whereIn('visibility', [
                ResourceVisibility::Enrolled->value,
                ResourceVisibility::Instructor->value,
                ResourceVisibility::Public->value,
            ]);
        }

        $resources = $query->get();

        return Inertia::render('Lesson/Show', [
            'lesson' => $lesson,
            'resources' => $resources,
            'canManage' => $user->can('update', $lesson),
        ]);
    }

    public function edit(Lesson $lesson): Response
    {
        $this->authorize('update', $lesson);

        $lesson->load('module.section.course');

        return Inertia::render('Lesson/Edit', [
            'lesson' => $lesson,
        ]);
    }

    public function update(UpdateLessonRequest $request, Lesson $lesson, UpdateLesson $action): RedirectResponse
    {
        $this->authorize('update', $lesson);

        $action->handle($lesson, $request->validated());

        return redirect()->route('lessons.show', $lesson)
            ->with('success', 'Lesson updated successfully.');
    }

    public function destroy(Lesson $lesson): RedirectResponse
    {
        $this->authorize('delete', $lesson);

        $moduleId = $lesson->module_id;
        $lesson->delete();

        return redirect()->route('modules.show', $moduleId)
            ->with('success', 'Lesson deleted.');
    }

    public function publish(Lesson $lesson, PublishLesson $action): RedirectResponse
    {
        $this->authorize('publish', $lesson);

        $action->handle($lesson);

        return redirect()->back()
            ->with('success', $lesson->published_at ? 'Lesson unpublished.' : 'Lesson published.');
    }
}
