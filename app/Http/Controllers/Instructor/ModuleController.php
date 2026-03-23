<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instructor;

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Module\Actions\CreateModule;
use App\Domain\Module\Actions\UpdateModule;
use App\Domain\Module\Models\Module;
use App\Http\Controllers\Controller;
use App\Http\Requests\Module\CreateModuleRequest;
use App\Http\Requests\Module\UpdateModuleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
            'assignments' => fn ($q) => $q->orderBy('due_at'),
            'assignments.submissions.grade',
            'announcements' => fn ($q) => $q->orderByDesc('published_at'),
            'enrollments' => fn ($q) => $q->where('status', 'active')->with('student:id,name,email,created_at'),
        ]);

        /** @var Collection<int, \App\Models\User> $students */
        $students = $section->enrollments->pluck('student');

        /** @var Collection<int, Assignment> $assignments */
        $assignments = $section->assignments->whereNotNull('published_at')->values();

        return Inertia::render('Instructor/Modules/Index', [
            'section'     => $section,
            'students'    => $students->values(),
            'assignments' => $assignments,
            'gradebook'   => $this->buildGradebook($students, $assignments),
        ]);
    }

    /**
     * @param  Collection<int, \App\Models\User>  $students
     * @param  Collection<int, Assignment>  $assignments
     * @return array<int, array<int, array{score: int|float|null, max_score: int|float, released: bool, submission_id: int|null}>>
     */
    private function buildGradebook(Collection $students, Collection $assignments): array
    {
        $gradebook = [];

        foreach ($students as $student) {
            $gradebook[$student->id] = [];

            foreach ($assignments as $assignment) {
                $submission = $assignment->submissions
                    ->where('student_id', $student->id)
                    ->first();

                $gradebook[$student->id][$assignment->id] = [
                    'score'         => $submission?->grade?->score,
                    'max_score'     => $assignment->max_score,
                    'released'      => $submission?->grade?->released_at !== null,
                    'submission_id' => $submission?->id,
                ];
            }
        }

        return $gradebook;
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
            'module'  => $module,
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
