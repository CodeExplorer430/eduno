<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Announcement\Models\Announcement;
use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Report\Actions\GetAdminReport;
use App\Domain\Submission\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request, GetAdminReport $adminReport): Response
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->isStudent()) {
            return $this->studentDashboard($user);
        }

        if ($user->isInstructor()) {
            return $this->instructorDashboard($user);
        }

        return Inertia::render('Dashboard', [
            'report' => $adminReport->handle(),
        ]);
    }

    private function studentDashboard(User $user): Response
    {
        $sectionIds = $user->enrollments()
            ->where('status', 'active')
            ->pluck('course_section_id');

        $enrolledSections = CourseSection::whereIn('id', $sectionIds)
            ->with('course')
            ->get();

        $upcomingAssignments = Assignment::whereIn('course_section_id', $sectionIds)
            ->whereNotNull('published_at')
            ->whereBetween('due_at', [now(), now()->addDays(7)])
            ->whereDoesntHave('submissions', fn ($q) => $q->where('student_id', $user->id))
            ->orderBy('due_at')
            ->limit(10)
            ->with('section.course')
            ->get();

        $recentAnnouncements = Announcement::whereIn('course_section_id', $sectionIds)
            ->whereNotNull('published_at')
            ->latest('published_at')
            ->limit(5)
            ->with('section.course')
            ->get();

        $recentGrades = Grade::whereHas('submission', fn ($q) => $q->where('student_id', $user->id))
            ->whereNotNull('released_at')
            ->latest('released_at')
            ->limit(5)
            ->with('submission.assignment.section.course')
            ->get();

        return Inertia::render('Dashboard', [
            'enrolledSections' => $enrolledSections,
            'upcomingAssignments' => $upcomingAssignments,
            'recentAnnouncements' => $recentAnnouncements,
            'recentGrades' => $recentGrades,
        ]);
    }

    private function instructorDashboard(User $user): Response
    {
        $sectionIds = CourseSection::where('instructor_id', $user->id)->pluck('id');

        $sections = CourseSection::where('instructor_id', $user->id)
            ->with('course')
            ->withCount(['enrollments', 'assignments'])
            ->get();

        $pendingSubmissions = Submission::whereHas(
            'assignment',
            fn ($q) => $q->whereIn('course_section_id', $sectionIds)
        )
            ->doesntHave('grade')
            ->latest('submitted_at')
            ->limit(10)
            ->with(['assignment.section.course', 'student'])
            ->get();

        return Inertia::render('Dashboard', [
            'sections' => $sections,
            'pendingSubmissions' => $pendingSubmissions,
        ]);
    }
}
