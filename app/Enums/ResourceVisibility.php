<?php

declare(strict_types=1);

namespace App\Enums;

enum ResourceVisibility: string
{
    case Enrolled = 'enrolled';
    case Instructor = 'instructor';
    case Public = 'public';
}
