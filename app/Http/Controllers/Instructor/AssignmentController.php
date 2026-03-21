<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instructor;

use App\Domain\Assignment\Actions\CreateAssignment;
use App\Domain\Assignment\Actions\UpdateAssignment;
use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\CourseSection;
use App\Http\Controllers\Controller;
use App\Http\Requests\Assignment\CreateAssignmentRequest;
use App\Http\Requests\Assignment\UpdateAssignmentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AssignmentController extends Controller
{
    public function __construct(
        private readonly CreateAssignment $createAssignment,
        private readonly UpdateAssignment $updateAssignment,
    ) {
    }

    public function index(Request $request, CourseSection $section): Response
    {
        abort_unless($request->user()->isInstructor() || $request->user()->isAdmin(), 403);
        $this->authorize('update', $section->assignments()->first() ?? new Assignment());

        $assignments = $section->assignments()->orderBy('due_at')->get();

        return Inertia::render('Instructor/Assignments/Index', [
            'section' => $section,
            'assignments' => $assignments,
        ]);
    }

    public function create(Request $request, CourseSection $section): Response
    {
        abort_unless($request->user()->isInstructor() || $request->user()->isAdmin(), 403);
        $this->authorize('create', [Assignment::class, $section]);

        return Inertia::render('Instructor/Assignments/Create', [
            'section' => $section,
        ]);
    }

    public function store(CreateAssignmentRequest $request, CourseSection $section): RedirectResponse
    {
        abort_unless($request->user()->isInstructor() || $request->user()->isAdmin(), 403);
        $this->authorize('create', [Assignment::class, $section]);

        $this->createAssignment->handle($section, $request->validated());

        return redirect()->route('instructor.courses.index')
            ->with('success', 'Assignment created.');
    }

    public function edit(Request $request, CourseSection $section, Assignment $assignment): Response
    {
        abort_unless($request->user()->isInstructor() || $request->user()->isAdmin(), 403);
        $this->authorize('update', $assignment);

        return Inertia::render('Instructor/Assignments/Edit', [
            'section' => $section,
            'assignment' => $assignment,
        ]);
    }

    public function update(UpdateAssignmentRequest $request, CourseSection $section, Assignment $assignment): RedirectResponse
    {
        abort_unless($request->user()->isInstructor() || $request->user()->isAdmin(), 403);
        $this->authorize('update', $assignment);

        $this->updateAssignment->handle($assignment, $request->validated());

        return redirect()->route('instructor.courses.index')
            ->with('success', 'Assignment updated.');
    }

    public function destroy(Request $request, CourseSection $section, Assignment $assignment): RedirectResponse
    {
        abort_unless($request->user()->isInstructor() || $request->user()->isAdmin(), 403);
        $this->authorize('delete', $assignment);

        $assignment->delete();

        return redirect()->route('instructor.courses.index')
            ->with('success', 'Assignment deleted.');
    }
}
