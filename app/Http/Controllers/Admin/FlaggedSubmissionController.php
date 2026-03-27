<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Submission\Models\Submission;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FlaggedSubmissionController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', User::class);

        $submissions = Submission::where('flagged_for_review', true)
            ->with(['student', 'assignment.courseSection.course'])
            ->orderByDesc('submitted_at')
            ->paginate(25);

        return Inertia::render('Admin/FlaggedSubmissions/Index', [
            'submissions' => $submissions,
        ]);
    }
}
