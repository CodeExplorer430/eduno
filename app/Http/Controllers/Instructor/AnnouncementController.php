<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instructor;

use App\Domain\Announcement\Actions\CreateAnnouncement;
use App\Domain\Announcement\Models\Announcement;
use App\Domain\Course\Models\CourseSection;
use App\Http\Controllers\Controller;
use App\Http\Requests\Announcement\CreateAnnouncementRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    public function __construct(private readonly CreateAnnouncement $createAnnouncement)
    {
    }

    public function index(Request $request): Response
    {
        abort_unless($request->user()->isInstructor() || $request->user()->isAdmin(), 403);

        $sectionIds = CourseSection::where('instructor_id', $request->user()->id)->pluck('id');
        $announcements = Announcement::whereIn('course_section_id', $sectionIds)
            ->with('courseSection.course')
            ->orderByDesc('published_at')
            ->get();

        return Inertia::render('Instructor/Announcements/Index', [
            'announcements' => $announcements,
        ]);
    }

    public function create(Request $request): Response
    {
        abort_unless($request->user()->isInstructor() || $request->user()->isAdmin(), 403);
        $this->authorize('create', Announcement::class);

        return Inertia::render('Instructor/Announcements/Create');
    }

    public function store(CreateAnnouncementRequest $request): RedirectResponse
    {
        abort_unless($request->user()->isInstructor() || $request->user()->isAdmin(), 403);
        $this->authorize('create', Announcement::class);

        $section = CourseSection::findOrFail($request->validated('course_section_id'));

        $this->createAnnouncement->execute($request->user(), $section, $request->validated());

        return redirect()->route('instructor.announcements.index')
            ->with('success', 'Announcement published.');
    }

    public function edit(Request $request, Announcement $announcement): Response
    {
        abort_unless($request->user()->isInstructor() || $request->user()->isAdmin(), 403);
        $this->authorize('update', $announcement);

        return Inertia::render('Instructor/Announcements/Edit', [
            'announcement' => $announcement,
        ]);
    }

    public function update(CreateAnnouncementRequest $request, Announcement $announcement): RedirectResponse
    {
        abort_unless($request->user()->isInstructor() || $request->user()->isAdmin(), 403);
        $this->authorize('update', $announcement);

        $announcement->update($request->validated());

        return redirect()->route('instructor.announcements.index')
            ->with('success', 'Announcement updated.');
    }

    public function destroy(Request $request, Announcement $announcement): RedirectResponse
    {
        abort_unless($request->user()->isInstructor() || $request->user()->isAdmin(), 403);
        $this->authorize('delete', $announcement);

        $announcement->delete();

        return redirect()->route('instructor.announcements.index')
            ->with('success', 'Announcement deleted.');
    }
}
