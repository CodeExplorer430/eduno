<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Assignment\Actions\CreateAssignment;
use App\Domain\Assignment\Actions\PublishAssignment;
use App\Domain\Assignment\Actions\UpdateAssignment;
use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\CourseSection;
use App\Http\Requests\Assignment\StoreAssignmentRequest;
use App\Http\Requests\Assignment\UpdateAssignmentRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AssignmentController extends Controller
{
    public function index(CourseSection $section): Response
    {
        $this->authorize('viewAny', [Assignment::class, $section]);

        /** @var User $user */
        $user = auth()->user();

        $section->load('course');

        $query = $section->assignments()->latest();

        if ($user->isStudent()) {
            $query->whereNotNull('published_at')->where('published_at', '<=', now());
        }

        $assignments = $query->paginate(15);

        if ($user->isStudent()) {
            $ids = $assignments->pluck('id');
            $submissionMap = $user->submissions()
                ->whereIn('assignment_id', $ids)
                ->get(['id', 'assignment_id', 'status', 'submitted_at', 'is_late', 'attempt_no'])
                ->keyBy('assignment_id');

            $assignments->through(function (Model $a, int $key) use ($submissionMap): Model {
                unset($key);
                $a->setAttribute('mySubmission', $submissionMap[$a->getKey()] ?? null);

                return $a;
            });
        }

        return Inertia::render('Assignment/Index', [
            'section' => $section,
            'assignments' => $assignments,
            'canManage' => $user->can('create', [Assignment::class, $section]),
        ]);
    }

    public function create(CourseSection $section): Response
    {
        $this->authorize('create', [Assignment::class, $section]);

        $section->load('course');

        return Inertia::render('Assignment/Create', [
            'section' => $section,
        ]);
    }

    public function store(
        StoreAssignmentRequest $request,
        CourseSection $section,
        CreateAssignment $action,
    ): RedirectResponse {
        $this->authorize('create', [Assignment::class, $section]);

        $action->handle($section, $request->validated());

        return redirect()->route('sections.assignments.index', $section)
            ->with('success', 'Assignment created successfully.');
    }

    public function show(Assignment $assignment): Response
    {
        $this->authorize('view', $assignment);

        /** @var User $user */
        $user = auth()->user();

        $assignment->load('section.course');

        $data = [
            'assignment' => $assignment,
            'canManage' => $user->can('update', $assignment),
        ];

        if ($user->isAdmin() || $user->id === $assignment->section->instructor_id) {
            $data['submissions'] = $assignment->submissions()
                ->with(['student', 'grade'])
                ->latest()
                ->get();
        } else {
            $data['mySubmission'] = $assignment->submissions()
                ->where('student_id', $user->id)
                ->with(['files', 'grade'])
                ->latest()
                ->first();
        }

        return Inertia::render('Assignment/Show', $data);
    }

    public function edit(Assignment $assignment): Response
    {
        $this->authorize('update', $assignment);

        $assignment->load('section.course');

        return Inertia::render('Assignment/Edit', [
            'assignment' => $assignment,
        ]);
    }

    public function update(
        UpdateAssignmentRequest $request,
        Assignment $assignment,
        UpdateAssignment $action,
    ): RedirectResponse {
        $this->authorize('update', $assignment);

        $action->handle($assignment, $request->validated());

        return redirect()->route('assignments.show', $assignment)
            ->with('success', 'Assignment updated successfully.');
    }

    public function destroy(Assignment $assignment): RedirectResponse
    {
        $this->authorize('delete', $assignment);

        $sectionId = $assignment->course_section_id;
        $assignment->delete();

        return redirect()->route('sections.assignments.index', $sectionId)
            ->with('success', 'Assignment deleted.');
    }

    public function publish(Assignment $assignment, PublishAssignment $action): RedirectResponse
    {
        $this->authorize('publish', $assignment);

        $action->handle($assignment);

        $message = $assignment->fresh()->published_at ? 'Assignment published.' : 'Assignment unpublished.';

        return redirect()->back()->with('success', $message);
    }
}
