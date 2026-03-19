<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Announcement\Actions\CreateAnnouncement;
use App\Domain\Announcement\Actions\PublishAnnouncement;
use App\Domain\Announcement\Actions\UpdateAnnouncement;
use App\Domain\Announcement\Models\Announcement;
use App\Domain\Course\Models\CourseSection;
use App\Http\Requests\Announcement\StoreAnnouncementRequest;
use App\Http\Requests\Announcement\UpdateAnnouncementRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    public function index(CourseSection $section): Response
    {
        $this->authorize('viewAny', [Announcement::class, $section]);

        /** @var User $user */
        $user = auth()->user();

        $section->load('course');

        $query = $section->announcements()->with('author')->latest();

        if ($user->isStudent()) {
            $query->whereNotNull('published_at')->where('published_at', '<=', now());
        }

        $announcements = $query->paginate(15);

        return Inertia::render('Announcement/Index', [
            'section' => $section,
            'announcements' => $announcements,
            'canManage' => $user->can('create', [Announcement::class, $section]),
        ]);
    }

    public function create(CourseSection $section): Response
    {
        $this->authorize('create', [Announcement::class, $section]);

        $section->load('course');

        return Inertia::render('Announcement/Create', [
            'section' => $section,
        ]);
    }

    public function store(
        StoreAnnouncementRequest $request,
        CourseSection $section,
        CreateAnnouncement $action,
    ): RedirectResponse {
        $this->authorize('create', [Announcement::class, $section]);

        /** @var User $user */
        $user = auth()->user();

        $action->handle($section, $request->validated(), $user);

        return redirect()->route('sections.announcements.index', $section)
            ->with('success', 'Announcement created successfully.');
    }

    public function show(Announcement $announcement): Response
    {
        $this->authorize('view', $announcement);

        /** @var User $user */
        $user = auth()->user();

        $announcement->load('section.course', 'author');

        return Inertia::render('Announcement/Show', [
            'announcement' => $announcement,
            'canManage' => $user->can('update', $announcement),
        ]);
    }

    public function edit(Announcement $announcement): Response
    {
        $this->authorize('update', $announcement);

        $announcement->load('section.course');

        return Inertia::render('Announcement/Edit', [
            'announcement' => $announcement,
        ]);
    }

    public function update(
        UpdateAnnouncementRequest $request,
        Announcement $announcement,
        UpdateAnnouncement $action,
    ): RedirectResponse {
        $this->authorize('update', $announcement);

        $action->handle($announcement, $request->validated());

        return redirect()->route('announcements.show', $announcement)
            ->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $this->authorize('delete', $announcement);

        $sectionId = $announcement->course_section_id;
        $announcement->delete();

        return redirect()->route('sections.announcements.index', $sectionId)
            ->with('success', 'Announcement deleted.');
    }

    public function publish(Announcement $announcement, PublishAnnouncement $action): RedirectResponse
    {
        $this->authorize('publish', $announcement);

        $action->handle($announcement);

        $message = $announcement->fresh()->published_at ? 'Announcement published.' : 'Announcement unpublished.';

        return redirect()->back()->with('success', $message);
    }
}
