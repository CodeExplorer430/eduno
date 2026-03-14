<?php

declare(strict_types=1);

namespace App\Support\Enums;

enum SubmissionStatus: string
{
    case Submitted = 'submitted';
    case Graded = 'graded';
    case Returned = 'returned';
}
