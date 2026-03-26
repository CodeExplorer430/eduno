<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instructor;

use App\Domain\Course\Models\CourseSection;
use App\Domain\Report\Actions\GetGradebook;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GradebookController extends Controller
{
    public function show(Request $request, CourseSection $section): Response
    {
        /** @var User $user */
        $user = $request->user();

        abort_unless($user->isInstructor() || $user->isAdmin(), 403);
        abort_unless($user->isAdmin() || $section->instructor_id === $user->id, 403);

        $data = (new GetGradebook())($section);

        return Inertia::render('Instructor/Gradebook/Index', $data);
    }

    public function export(Request $request, CourseSection $section): StreamedResponse
    {
        /** @var User $user */
        $user = $request->user();

        abort_unless($user->isInstructor() || $user->isAdmin(), 403);
        abort_unless($user->isAdmin() || $section->instructor_id === $user->id, 403);

        $data = (new GetGradebook())($section);

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="gradebook.csv"',
        ];

        return response()->stream(function () use ($data): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fputcsv($handle, [
                'Student',
                ...$data['assignments']->pluck('title')->toArray(),
                'Total',
            ]);

            foreach ($data['students'] as $student) {
                $row = [$student['name']];
                foreach ($data['assignments'] as $assignment) {
                    $cell  = $student['grades'][$assignment->id] ?? null;
                    $row[] = $cell !== null ? $cell['score'] : '';
                }
                $row[] = $student['total'];
                fputcsv($handle, $row);
            }

            $avgRow = ['Average'];
            foreach ($data['assignments'] as $assignment) {
                $avgRow[] = $data['averages'][$assignment->id] ?? '';
            }
            $avgRow[] = '';
            fputcsv($handle, $avgRow);

            fclose($handle);
        }, 200, $headers);
    }
}
