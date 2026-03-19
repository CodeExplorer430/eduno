<?php

declare(strict_types=1);

namespace App\Domain\Module\Actions;

use App\Domain\Module\Models\Lesson;
use Illuminate\Support\Facades\Log;

class PublishLesson
{
    public function handle(Lesson $lesson): Lesson
    {
        if ($lesson->published_at !== null) {
            Log::info('Lesson unpublished', ['lesson_id' => $lesson->id]);
            $lesson->published_at = null;
        } else {
            Log::info('Lesson published', ['lesson_id' => $lesson->id]);
            $lesson->published_at = now();
        }

        $lesson->save();

        return $lesson;
    }
}
