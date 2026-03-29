<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instructor;

use App\Domain\Course\Actions\EnrollStudent;
use App\Domain\Course\Actions\UnenrollStudent;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RosterController extends Controller
{
    public function index(Request $request, CourseSection $section): Response
    {
        $this->authorize('update', $section);

        $section->load('course');

        /** @var \Illuminate\Support\Collection<int, Enrollment> $enrollments */
        $enrollments = $section->enrollments()
            ->where('status', 'active')
            ->with('student:id,name,email')
            ->orderBy('enrolled_at')
            ->get();

        $students = $enrollments->map(fn (Enrollment $e) => [
            'enrollment_id' => $e->id,
            'id'            => $e->student->id,
            'name'          => $e->student->name,
            'email'         => $e->student->email,
            'enrolled_at'   => $e->enrolled_at,
        ]);

        return Inertia::render('Instructor/Roster/Index', [
            'section'  => $section,
            'students' => $students,
        ]);
    }

    public function store(Request $request, CourseSection $section, EnrollStudent $action): RedirectResponse
    {
        $this->authorize('update', $section);

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $student = User::where('email', $validated['email'])
            ->where('role', UserRole::Student)
            ->firstOrFail();

        $action->handle($student, $section);

        return redirect()
            ->route('instructor.roster.index', $section)
            ->with('success', "{$student->name} enrolled.");
    }

    public function destroy(Request $request, CourseSection $section, Enrollment $enrollment, UnenrollStudent $action): RedirectResponse
    {
        $this->authorize('update', $section);

        abort_if($enrollment->course_section_id !== $section->id, 404);

        $action->handle($enrollment);

        return redirect()
            ->route('instructor.roster.index', $section)
            ->with('success', 'Student removed from section.');
    }
}
