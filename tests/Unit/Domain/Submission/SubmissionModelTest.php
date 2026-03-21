<?php

declare(strict_types=1);

use App\Domain\Submission\Models\Submission;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

it('has expected fillable attributes', function (): void {
    $model = new Submission();
    expect($model->getFillable())->toContain(
        'assignment_id',
        'student_id',
        'status',
        'submitted_at',
        'is_late',
    );
});

it('uses assignment relationship', function (): void {
    $submission = new Submission();
    expect($submission->assignment())->toBeInstanceOf(BelongsTo::class);
});

it('uses student relationship', function (): void {
    $submission = new Submission();
    expect($submission->student())->toBeInstanceOf(BelongsTo::class);
});

it('uses files relationship', function (): void {
    $submission = new Submission();
    expect($submission->files())->toBeInstanceOf(HasMany::class);
});

it('uses grade relationship', function (): void {
    $submission = new Submission();
    expect($submission->grade())->toBeInstanceOf(HasOne::class);
});
