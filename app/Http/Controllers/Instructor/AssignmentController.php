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
    ) {}

    public function index(Request $request, CourseSection $course): Response
    {
        $this->authorize('viewAny', Assignment::class);

        $assignments = $course->assignments()->orderBy('due_at')->get();

        return Inertia::render('Instructor/Assignments/Index', [
            'section' => $course,
            'assignments' => $assignments,
        ]);
    }

    public function create(Request $request, CourseSection $course): Response
    {
        $this->authorize('create', Assignment::class);

        return Inertia::render('Instructor/Assignments/Create', [
            'section' => $course,
        ]);
    }

    public function store(CreateAssignmentRequest $request, CourseSection $course): RedirectResponse
    {
        $this->authorize('create', Assignment::class);

        $this->createAssignment->execute($course, $request->validated());

        return redirect()->route('instructor.courses.index')
            ->with('success', 'Assignment created.');
    }

    public function edit(Request $request, Assignment $assignment): Response
    {
        $this->authorize('update', $assignment);

        return Inertia::render('Instructor/Assignments/Edit', [
            'section' => $assignment->courseSection,
            'assignment' => $assignment,
        ]);
    }

    public function update(UpdateAssignmentRequest $request, Assignment $assignment): RedirectResponse
    {
        $this->authorize('update', $assignment);

        $this->updateAssignment->execute($assignment, $request->validated());

        return redirect()->route('instructor.courses.index')
            ->with('success', 'Assignment updated.');
    }

    public function destroy(Request $request, Assignment $assignment): RedirectResponse
    {
        $this->authorize('delete', $assignment);

        $assignment->delete();

        return redirect()->route('instructor.courses.index')
            ->with('success', 'Assignment deleted.');
    }
}
