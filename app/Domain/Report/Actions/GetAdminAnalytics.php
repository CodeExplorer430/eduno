<?php

declare(strict_types=1);

namespace App\Domain\Report\Actions;

use App\Domain\Submission\Models\Grade;
use App\Domain\Submission\Models\Submission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GetAdminAnalytics
{
    /**
     * @return array{
     *     submission_trend: array{labels: list<string>, onTime: list<int>, late: list<int>},
     *     grade_distribution: array{labels: list<string>, counts: list<int>},
     *     late_rate_by_course: array{labels: list<string>, rates: list<float>},
     *     status_breakdown: array{labels: list<string>, counts: list<int>}
     * }
     */
    public function handle(): array
    {
        return Cache::remember('report.admin.analytics', now()->addMinutes(10), function (): array {
            return [
                'submission_trend'    => $this->submissionTrend(),
                'grade_distribution'  => $this->gradeDistribution(),
                'late_rate_by_course' => $this->lateRateByCourse(),
                'status_breakdown'    => $this->statusBreakdown(),
            ];
        });
    }

    /**
     * @return array{labels: list<string>, onTime: list<int>, late: list<int>}
     */
    private function submissionTrend(): array
    {
        $labels = [];
        $onTime = [];
        $late   = [];

        for ($i = 7; $i >= 0; $i--) {
            $start = now()->startOfWeek()->subWeeks($i);
            $end   = $start->copy()->endOfWeek();

            $total     = Submission::whereBetween('submitted_at', [$start, $end])->count();
            $lateCount = Submission::whereBetween('submitted_at', [$start, $end])
                ->where('is_late', true)
                ->count();

            $labels[] = $start->format('M d');
            $onTime[] = $total - $lateCount;
            $late[]   = $lateCount;
        }

        return compact('labels', 'onTime', 'late');
    }

    /**
     * @return array{labels: list<string>, counts: list<int>}
     */
    private function gradeDistribution(): array
    {
        $scores = Grade::pluck('score');

        return [
            'labels' => ['0–49', '50–59', '60–69', '70–79', '80–89', '90–100'],
            'counts' => [
                $scores->filter(fn ($s) => $s < 50)->count(),
                $scores->filter(fn ($s) => $s >= 50 && $s < 60)->count(),
                $scores->filter(fn ($s) => $s >= 60 && $s < 70)->count(),
                $scores->filter(fn ($s) => $s >= 70 && $s < 80)->count(),
                $scores->filter(fn ($s) => $s >= 80 && $s < 90)->count(),
                $scores->filter(fn ($s) => $s >= 90)->count(),
            ],
        ];
    }

    /**
     * @return array{labels: list<string>, rates: list<float>}
     */
    private function lateRateByCourse(): array
    {
        $rows = DB::table('submissions')
            ->join('assignments', 'submissions.assignment_id', '=', 'assignments.id')
            ->join('course_sections', 'assignments.course_section_id', '=', 'course_sections.id')
            ->join('courses', 'course_sections.course_id', '=', 'courses.id')
            ->whereNull('submissions.deleted_at')
            ->select(
                'courses.code',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN submissions.is_late THEN 1 ELSE 0 END) as late_count')
            )
            ->groupBy('courses.id', 'courses.code')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $labels = [];
        $rates  = [];

        foreach ($rows as $row) {
            $labels[] = $row->code;
            $rates[]  = $row->total > 0
                ? round(($row->late_count / $row->total) * 100, 1)
                : 0.0;
        }

        return compact('labels', 'rates');
    }

    /**
     * @return array{labels: list<string>, counts: list<int>}
     */
    private function statusBreakdown(): array
    {
        $rows = Submission::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return [
            'labels' => $rows->keys()->all(),
            'counts' => $rows->values()->map(fn ($v) => (int) $v)->all(),
        ];
    }
}
