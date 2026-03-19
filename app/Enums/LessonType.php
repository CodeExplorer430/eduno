<?php

declare(strict_types=1);

namespace App\Enums;

enum LessonType: string
{
    case Text = 'text';
    case Pdf = 'pdf';
    case Video = 'video';
    case Link = 'link';
}
