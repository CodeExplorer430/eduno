<?php

declare(strict_types=1);

use App\Domain\Announcement\Models\Announcement;
use App\Domain\Course\Models\Course;
use App\Domain\Course\Models\CourseSection;
use App\Domain\Course\Models\Enrollment;
use App\Enums\UserRole;
use App\Models\User;

// ─── Helpers ─────────────────────────────────────────────────────────────────

function makeAnnouncementSection(): array
{
    $instructor = User::factory()->create(['role' => UserRole::Instructor]);
    $course = Course::create([
        'code' => 'ANN'.fake()->unique()->numberBetween(100, 999),
        'title' => 'Test Course',
        'department' => 'CCS',
        'term' => '1st Semester',
        'academic_year' => '2025-2026',
        'status' => 'published',
        'created_by' => $instructor->id,
    ]);
    $section = CourseSection::create([
        'course_id' => $course->id,
        'section_name' => 'Section A',
        'instructor_id' => $instructor->id,
    ]);

    return [$instructor, $section];
}

function makeAnnouncement(CourseSection $section, User $author, bool $published = false): Announcement
{
    return Announcement::create([
        'course_section_id' => $section->id,
        'title' => 'Test Announcement',
        'body' => 'This is the announcement body.',
        'created_by' => $author->id,
        'published_at' => $published ? now()->subMinute() : null,
    ]);
}

function enrollStudentInSection(User $student, CourseSection $section): void
{
    Enrollment::create([
        'user_id' => $student->id,
        'course_section_id' => $section->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);
}

// ─── Auth ─────────────────────────────────────────────────────────────────────

test('unauthenticated user is redirected from announcements index', function (): void {
    [, $section] = makeAnnouncementSection();

    $this->get(route('sections.announcements.index', $section))
        ->assertRedirect(route('login'));
});

// ─── CRUD ─────────────────────────────────────────────────────────────────────

test('instructor can create an announcement', function (): void {
    [$instructor, $section] = makeAnnouncementSection();

    $this->actingAs($instructor)
        ->post(route('sections.announcements.store', $section), [
            'title' => 'New Announcement',
            'body' => 'Some content here.',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('announcements', [
        'course_section_id' => $section->id,
        'title' => 'New Announcement',
        'created_by' => $instructor->id,
    ]);
});

test('student cannot create an announcement', function (): void {
    [, $section] = makeAnnouncementSection();
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->post(route('sections.announcements.store', $section), [
            'title' => 'Hack',
            'body' => 'Attempt',
        ])
        ->assertForbidden();
});

test('instructor can update their announcement', function (): void {
    [$instructor, $section] = makeAnnouncementSection();
    $announcement = makeAnnouncement($section, $instructor);

    $this->actingAs($instructor)
        ->put(route('announcements.update', $announcement), [
            'title' => 'Updated Title',
            'body' => 'Updated body.',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('announcements', [
        'id' => $announcement->id,
        'title' => 'Updated Title',
    ]);
});

test('instructor can delete their announcement', function (): void {
    [$instructor, $section] = makeAnnouncementSection();
    $announcement = makeAnnouncement($section, $instructor);

    $this->actingAs($instructor)
        ->delete(route('announcements.destroy', $announcement))
        ->assertRedirect();

    $this->assertDatabaseMissing('announcements', ['id' => $announcement->id]);
});

// ─── Visibility ───────────────────────────────────────────────────────────────

test('student sees only published announcements in index', function (): void {
    [$instructor, $section] = makeAnnouncementSection();
    $published = makeAnnouncement($section, $instructor, true);
    $draft = makeAnnouncement($section, $instructor, false);

    $student = User::factory()->create(['role' => UserRole::Student]);
    enrollStudentInSection($student, $section);

    $response = $this->actingAs($student)
        ->get(route('sections.announcements.index', $section))
        ->assertOk();

    $announcementsPaginated = $response->original->getData()['page']['props']['announcements'];
    $ids = collect($announcementsPaginated['data'])->pluck('id')->all();
    expect($ids)->toContain($published->id)
        ->and($ids)->not->toContain($draft->id);
});

test('enrolled student can view a published announcement', function (): void {
    [$instructor, $section] = makeAnnouncementSection();
    $announcement = makeAnnouncement($section, $instructor, true);
    $student = User::factory()->create(['role' => UserRole::Student]);
    enrollStudentInSection($student, $section);

    $this->actingAs($student)
        ->get(route('announcements.show', $announcement))
        ->assertOk();
});

test('enrolled student cannot view a draft announcement', function (): void {
    [$instructor, $section] = makeAnnouncementSection();
    $announcement = makeAnnouncement($section, $instructor, false);
    $student = User::factory()->create(['role' => UserRole::Student]);
    enrollStudentInSection($student, $section);

    $this->actingAs($student)
        ->get(route('announcements.show', $announcement))
        ->assertForbidden();
});

// ─── Publish / Unpublish ──────────────────────────────────────────────────────

test('instructor can publish an announcement', function (): void {
    [$instructor, $section] = makeAnnouncementSection();
    $announcement = makeAnnouncement($section, $instructor, false);

    $this->actingAs($instructor)
        ->post(route('announcements.publish', $announcement))
        ->assertRedirect();

    expect($announcement->fresh()->published_at)->not->toBeNull();
});

test('instructor can unpublish an announcement', function (): void {
    [$instructor, $section] = makeAnnouncementSection();
    $announcement = makeAnnouncement($section, $instructor, true);

    $this->actingAs($instructor)
        ->post(route('announcements.publish', $announcement))
        ->assertRedirect();

    expect($announcement->fresh()->published_at)->toBeNull();
});

test('student cannot publish an announcement', function (): void {
    [$instructor, $section] = makeAnnouncementSection();
    $announcement = makeAnnouncement($section, $instructor, false);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->post(route('announcements.publish', $announcement))
        ->assertForbidden();
});

// ─── Ownership (non-owning instructor) ───────────────────────────────────────

test('non-owning instructor cannot create announcement in another section', function (): void {
    [, $section] = makeAnnouncementSection();
    $other = User::factory()->create(['role' => UserRole::Instructor]);

    $this->actingAs($other)
        ->post(route('sections.announcements.store', $section), [
            'title' => 'Hijack',
            'body'  => 'Attempt',
        ])
        ->assertForbidden();
});

test('non-owning instructor cannot update another instructor\'s announcement', function (): void {
    [$instructor, $section] = makeAnnouncementSection();
    $announcement = makeAnnouncement($section, $instructor);
    $other = User::factory()->create(['role' => UserRole::Instructor]);

    $this->actingAs($other)
        ->put(route('announcements.update', $announcement), [
            'title' => 'Hijack',
            'body'  => 'Attempt',
        ])
        ->assertForbidden();
});

test('non-owning instructor cannot delete another instructor\'s announcement', function (): void {
    [$instructor, $section] = makeAnnouncementSection();
    $announcement = makeAnnouncement($section, $instructor);
    $other = User::factory()->create(['role' => UserRole::Instructor]);

    $this->actingAs($other)
        ->delete(route('announcements.destroy', $announcement))
        ->assertForbidden();
});

test('non-owning instructor cannot publish another instructor\'s announcement', function (): void {
    [$instructor, $section] = makeAnnouncementSection();
    $announcement = makeAnnouncement($section, $instructor, false);
    $other = User::factory()->create(['role' => UserRole::Instructor]);

    $this->actingAs($other)
        ->post(route('announcements.publish', $announcement))
        ->assertForbidden();
});

// ─── Validation ───────────────────────────────────────────────────────────────

test('announcement creation fails with missing title', function (): void {
    [$instructor, $section] = makeAnnouncementSection();

    $this->actingAs($instructor)
        ->post(route('sections.announcements.store', $section), ['body' => 'Body only'])
        ->assertSessionHasErrors(['title']);
});

test('announcement creation fails with missing body', function (): void {
    [$instructor, $section] = makeAnnouncementSection();

    $this->actingAs($instructor)
        ->post(route('sections.announcements.store', $section), ['title' => 'Title only'])
        ->assertSessionHasErrors(['body']);
});

// ─── Role / Enrollment gates ──────────────────────────────────────────────────

test('student cannot update an announcement', function (): void {
    [$instructor, $section] = makeAnnouncementSection();
    $announcement = makeAnnouncement($section, $instructor);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->put(route('announcements.update', $announcement), [
            'title' => 'Hack',
            'body'  => 'Attempt',
        ])
        ->assertForbidden();
});

test('student cannot delete an announcement', function (): void {
    [$instructor, $section] = makeAnnouncementSection();
    $announcement = makeAnnouncement($section, $instructor);
    $student = User::factory()->create(['role' => UserRole::Student]);

    $this->actingAs($student)
        ->delete(route('announcements.destroy', $announcement))
        ->assertForbidden();
});

test('unenrolled student cannot view a published announcement', function (): void {
    [$instructor, $section] = makeAnnouncementSection();
    $announcement = makeAnnouncement($section, $instructor, true);
    $student = User::factory()->create(['role' => UserRole::Student]);
    // student is NOT enrolled

    $this->actingAs($student)
        ->get(route('announcements.show', $announcement))
        ->assertForbidden();
});
