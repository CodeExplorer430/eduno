<?php

declare(strict_types=1);

namespace App\Http\Controllers\Student;

use App\Domain\Assignment\Actions\GetUpcomingDeadlines;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request, GetUpcomingDeadlines $getUpcomingDeadlines): Response
    {
        /** @var User $user */
        $user = $request->user();

        $upcoming = $getUpcomingDeadlines->execute($user);

        return Inertia::render('Dashboard', [
            'upcoming' => $upcoming,
            'recentGrades' => [],
            'courseSummary' => [],
        ]);
    }
}
