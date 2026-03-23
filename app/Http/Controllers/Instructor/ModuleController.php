<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instructor;

use App\Domain\Module\Actions\CreateModule;
use App\Domain\Module\Actions\UpdateModule;
use App\Domain\Module\Models\Module;
use App\Domain\Course\Models\CourseSection;
use App\Http\Controllers\Controller;
use App\Http\Requests\Module\CreateModuleRequest;
use App\Http\Requests\Module\UpdateModuleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ModuleController extends Controller
{
    public function index(Request $request, CourseSection $section): Response
    {
        $this->authorize('update', $section->course);

        $section->load([
            'course',
            'modules' => fn ($q) => $q->orderBy('order_no'),
            'modules.lessons' => fn ($q) => $q->orderBy('order_no'),
            'modules.lessons.resources',
        ]);

        return Inertia::render('Instructor/Modules/Index', [
            'section' => $section,
        ]);
    }

    public function create(Request $request, CourseSection $section): Response
    {
        $this->authorize('create', Module::class);

        return Inertia::render('Instructor/Modules/Create', [
            'section' => $section,
        ]);
    }

    public function store(CreateModuleRequest $request, CourseSection $section, CreateModule $action): RedirectResponse
    {
        $this->authorize('create', Module::class);

        $action->handle($section, $request->validated());

        return redirect()
            ->route('instructor.courses.modules.index', $section)
            ->with('success', 'Module created.');
    }

    public function edit(Request $request, CourseSection $section, Module $module): Response
    {
        $this->authorize('update', $module);

        return Inertia::render('Instructor/Modules/Edit', [
            'section' => $section,
            'module' => $module,
        ]);
    }

    public function update(UpdateModuleRequest $request, CourseSection $section, Module $module, UpdateModule $action): RedirectResponse
    {
        $this->authorize('update', $module);

        $action->handle($module, $request->validated());

        return redirect()
            ->route('instructor.courses.modules.index', $section)
            ->with('success', 'Module updated.');
    }

    public function destroy(Request $request, CourseSection $section, Module $module): RedirectResponse
    {
        $this->authorize('delete', $module);

        $module->delete();

        return redirect()
            ->route('instructor.courses.modules.index', $section)
            ->with('success', 'Module deleted.');
    }
}
