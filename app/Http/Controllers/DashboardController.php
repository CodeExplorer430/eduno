<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Announcement\Models\Announcement;
use App\Domain\Assignment\Models\Assignment;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Grade\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        return match ($user->role) {
            UserRole::Student => $this->studentDashboard($user),
            UserRole::Instructor => $this->instructorDashboard($user),
            UserRole::Admin => $this->adminDashboard(),
        };
    }

    private function studentDashboard(User $user): Response
    {
        $sectionIds = $user->enrollments()->pluck('course_section_id');

        $upcomingAssignments = Assignment::whereIn('course_section_id', $sectionIds)
            ->whereNotNull('published_at')
            ->where('due_at', '>=', now())
            ->where('due_at', '<=', now()->addDays(7))
            ->orderBy('due_at')
            ->limit(5)
            ->get();

        $recentAnnouncements = Announcement::whereIn('course_section_id', $sectionIds)
            ->whereNotNull('published_at')
            ->with(['courseSection.course', 'author'])
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $latestGrade = Grade::whereHas('submission', fn ($q) => $q->where('student_id', $user->id))
            ->whereNotNull('released_at')
            ->with(['submission.assignment'])
            ->orderByDesc('released_at')
            ->first();

        return Inertia::render('Dashboard', [
            'role' => 'student',
            'enrolled_courses_count' => $user->enrollments()->count(),
            'upcoming_assignments' => $upcomingAssignments,
            'recent_announcements' => $recentAnnouncements,
            'latest_grade' => $latestGrade,
        ]);
    }

    private function instructorDashboard(User $user): Response
    {
        $sectionIds = CourseSection::where('instructor_id', $user->id)->pluck('id');

        $pendingSubmissionsCount = Submission::whereHas(
            'assignment',
            fn ($q) => $q->whereIn('course_section_id', $sectionIds)
        )
            ->whereDoesntHave('grade')
            ->where('status', 'submitted')
            ->count();

        $recentSubmissions = Submission::whereHas(
            'assignment',
            fn ($q) => $q->whereIn('course_section_id', $sectionIds)
        )
            ->with(['assignment', 'student'])
            ->orderByDesc('submitted_at')
            ->limit(5)
            ->get();

        $upcomingDeadlines = Assignment::whereIn('course_section_id', $sectionIds)
            ->whereNotNull('published_at')
            ->where('due_at', '>=', now())
            ->where('due_at', '<=', now()->addDays(7))
            ->orderBy('due_at')
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard', [
            'role' => 'instructor',
            'courses_count' => $sectionIds->count(),
            'pending_submissions_count' => $pendingSubmissionsCount,
            'recent_submissions' => $recentSubmissions,
            'upcoming_deadlines' => $upcomingDeadlines,
        ]);
    }

    private function adminDashboard(): Response
    {
        $usersByRole = [
            'student' => User::where('role', UserRole::Student)->count(),
            'instructor' => User::where('role', UserRole::Instructor)->count(),
            'admin' => User::where('role', UserRole::Admin)->count(),
        ];

        return Inertia::render('Dashboard', [
            'role' => 'admin',
            'users_by_role' => $usersByRole,
            'total_courses' => Course::count(),
            'total_submissions' => Submission::count(),
            'total_grades_released' => Grade::whereNotNull('released_at')->count(),
        ]);
    }
}
