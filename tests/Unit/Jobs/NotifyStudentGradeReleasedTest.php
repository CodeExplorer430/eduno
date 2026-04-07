<?php

declare(strict_types=1);

use App\Domain\Accessibility\Models\UserPreference;
use App\Domain\Submission\Models\Grade;
use App\Domain\Submission\Models\Submission;
use App\Jobs\NotifyStudentGradeReleased;
use App\Mail\GradeReleasedMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

it('can be instantiated', function () {
    expect(NotifyStudentGradeReleased::class)->toBeString();
});

it('uses the queue', function () {
    $interfaces = class_implements(NotifyStudentGradeReleased::class);
    expect($interfaces)->toContain(ShouldQueue::class);
});

it('sends GradeReleasedMail when student has no preferences', function () {
    Mail::fake();

    $student = User::factory()->make(['email' => 'student@example.com']);
    $student->setRelation('preferences', null);

    $submission = new Submission();
    $submission->setRelation('student', $student);

    $grade = new Grade();
    $grade->setRelation('submission', $submission);

    (new NotifyStudentGradeReleased($grade))->handle();

    Mail::assertSent(GradeReleasedMail::class, fn ($mail) => $mail->hasTo('student@example.com'));
});

it('sends GradeReleasedMail when email_notifications is true', function () {
    Mail::fake();

    $student = User::factory()->make(['email' => 'student@example.com']);
    $student->setRelation('preferences', new UserPreference(['email_notifications' => true]));

    $submission = new Submission();
    $submission->setRelation('student', $student);

    $grade = new Grade();
    $grade->setRelation('submission', $submission);

    (new NotifyStudentGradeReleased($grade))->handle();

    Mail::assertSent(GradeReleasedMail::class);
});

it('skips mail when student email_notifications is false', function () {
    Mail::fake();

    $student = User::factory()->make(['email' => 'student@example.com']);
    $student->setRelation('preferences', new UserPreference(['email_notifications' => false]));

    $submission = new Submission();
    $submission->setRelation('student', $student);

    $grade = new Grade();
    $grade->setRelation('submission', $submission);

    (new NotifyStudentGradeReleased($grade))->handle();

    Mail::assertNotSent(GradeReleasedMail::class);
});
