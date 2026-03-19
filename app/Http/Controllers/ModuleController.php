<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Course\Models\CourseSection;
use App\Domain\Module\Actions\CreateModule;
use App\Domain\Module\Actions\PublishModule;
use App\Domain\Module\Actions\UpdateModule;
use App\Domain\Module\Models\Module;
use App\Http\Requests\Module\StoreModuleRequest;
use App\Http\Requests\Module\UpdateModuleRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ModuleController extends Controller
{
    public function index(CourseSection $section): Response
    {
        $this->authorize('viewAny', [Module::class, $section]);

        /** @var User $user */
        $user = auth()->user();

        $section->load('course');

        $query = $section->modules()->orderBy('order_no');

        if ($user->isStudent()) {
            $query->whereNotNull('published_at')->where('published_at', '<=', now());
        }

        $modules = $query->get();

        return Inertia::render('Module/Index', [
            'section' => $section,
            'modules' => $modules,
            'canManage' => $user->can('create', [Module::class, $section]),
        ]);
    }

    public function create(CourseSection $section): Response
    {
        $this->authorize('create', [Module::class, $section]);

        $section->load('course');

        return Inertia::render('Module/Create', [
            'section' => $section,
        ]);
    }

    public function store(StoreModuleRequest $request, CourseSection $section, CreateModule $action): RedirectResponse
    {
        $this->authorize('create', [Module::class, $section]);

        $action->handle($section, $request->validated());

        return redirect()->route('sections.modules.index', $section)
            ->with('success', 'Module created successfully.');
    }

    public function show(Module $module): Response
    {
        $this->authorize('view', $module);

        /** @var User $user */
        $user = auth()->user();

        $module->load('section.course');

        $query = $module->lessons()->orderBy('order_no');

        if ($user->isStudent()) {
            $query->whereNotNull('published_at')->where('published_at', '<=', now());
        }

        $lessons = $query->get();

        return Inertia::render('Module/Show', [
            'module' => $module,
            'lessons' => $lessons,
            'canManage' => $user->can('update', $module),
        ]);
    }

    public function edit(Module $module): Response
    {
        $this->authorize('update', $module);

        $module->load('section.course');

        return Inertia::render('Module/Edit', [
            'module' => $module,
        ]);
    }

    public function update(UpdateModuleRequest $request, Module $module, UpdateModule $action): RedirectResponse
    {
        $this->authorize('update', $module);

        $action->handle($module, $request->validated());

        return redirect()->route('modules.show', $module)
            ->with('success', 'Module updated successfully.');
    }

    public function destroy(Module $module): RedirectResponse
    {
        $this->authorize('delete', $module);

        $sectionId = $module->course_section_id;
        $module->delete();

        return redirect()->route('sections.modules.index', $sectionId)
            ->with('success', 'Module deleted.');
    }

    public function publish(Module $module, PublishModule $action): RedirectResponse
    {
        $this->authorize('publish', $module);

        $action->handle($module);

        return redirect()->back()
            ->with('success', $module->published_at ? 'Module unpublished.' : 'Module published.');
    }
}
