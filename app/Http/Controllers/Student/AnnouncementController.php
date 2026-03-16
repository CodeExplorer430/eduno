<?php

declare(strict_types=1);

namespace App\Http\Controllers\Student;

use App\Domain\Announcement\Models\Announcement;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    public function index(Request $request): Response
    {
        $sectionIds = $request->user()->enrollments()->pluck('course_section_id');

        $announcements = Announcement::whereIn('course_section_id', $sectionIds)
            ->whereNotNull('published_at')
            ->with(['courseSection.course', 'author'])
            ->orderByDesc('published_at')
            ->paginate(20);

        return Inertia::render('Student/Announcements/Index', [
            'announcements' => $announcements,
        ]);
    }
}
