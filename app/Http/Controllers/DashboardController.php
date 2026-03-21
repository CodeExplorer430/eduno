<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Announcement\Models\Announcement;
use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
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

        $report = $adminReport->handle();

        return Inertia::render('Dashboard', [
            'role' => 'admin',
            'users_by_role' => User::selectRaw('role, count(*) as count')->groupBy('role')->pluck('count', 'role'),
            'total_courses' => $report['total_courses'],
            'total_submissions' => $report['total_submissions'],
            'total_grades_released' => Grade::whereNotNull('released_at')->count(),
            'report' => $report,
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

        $recentGrades = Grade::whereHas('submission', fn ($q) => $q->where('student_id', $user->id))
            ->whereNotNull('released_at')
            ->latest('released_at')
            ->limit(5)
            ->with('submission.assignment.section.course')
            ->get();

        $recentAnnouncements = Announcement::whereIn('course_section_id', $sectionIds)
            ->whereNotNull('published_at')
            ->latest('published_at')
            ->limit(5)
            ->get(['id', 'title', 'published_at', 'course_section_id']);

        return Inertia::render('Dashboard', [
            'role' => 'student',
            'enrolled_courses_count' => $enrolledSections->count(),
            'upcoming_assignments' => $upcomingAssignments,
            'recent_announcements' => $recentAnnouncements,
            'courseSummary' => $enrolledSections->map(function (CourseSection $s): array {
                /** @var Course $course */
                $course = $s->course;

                return [
                    'id' => $s->id,
                    'code' => $course->code,
                    'title' => $course->title,
                    'section_name' => $s->section_name,
                ];
            }),
            'upcoming' => $upcomingAssignments->map(function (Assignment $a): array {
                /** @var CourseSection $section */
                $section = $a->section;
                /** @var Course $course */
                $course = $section->course;

                return [
                    'id' => $a->id,
                    'title' => $a->title,
                    'course_name' => $course->title,
                    'course_code' => $course->code,
                    'due_at' => $a->due_at,
                ];
            }),
            'recentGrades' => $recentGrades->map(function (Grade $g): array {
                $assignment = $g->submission->assignment;
                /** @var Course $course */
                $course = $assignment->section->course;

                return [
                    'assignment_title' => $assignment->title,
                    'score' => $g->score,
                    'max_score' => $assignment->max_score,
                    'course_name' => $course->title,
                ];
            }),
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

        $upcomingDeadlines = Assignment::whereIn('course_section_id', $sectionIds)
            ->whereNotNull('published_at')
            ->whereBetween('due_at', [now(), now()->addDays(7)])
            ->orderBy('due_at')
            ->limit(10)
            ->get(['id', 'title', 'due_at', 'course_section_id']);

        return Inertia::render('Dashboard', [
            'role' => 'instructor',
            'courses_count' => $sections->count(),
            'pending_submissions_count' => $pendingSubmissions->count(),
            'recent_submissions' => $pendingSubmissions,
            'upcoming_deadlines' => $upcomingDeadlines,
            'sections' => $sections,
            'pendingSubmissions' => $pendingSubmissions,
        ]);
    }
}
