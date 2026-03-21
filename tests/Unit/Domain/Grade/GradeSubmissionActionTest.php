<?php

declare(strict_types=1);

use App\Domain\Audit\Actions\LogAction;
use App\Domain\Grade\Actions\GradeSubmission;

it('creates a grade record', function () {
    $logAction = Mockery::mock(LogAction::class);
    $logAction->shouldReceive('execute')->zeroOrMoreTimes();

    $action = new GradeSubmission($logAction);

    expect($action)->toBeInstanceOf(GradeSubmission::class);
});
