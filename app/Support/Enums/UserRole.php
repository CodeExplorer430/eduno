<?php

declare(strict_types=1);

namespace App\Support\Enums;

enum UserRole: string
{
    case Student = 'student';
    case Instructor = 'instructor';
    case Admin = 'admin';
}
