<?php

declare(strict_types=1);

namespace App\Domain\Content\Actions;

use App\Domain\Content\Models\Lesson;

class CreateLesson
{
    /**
     * @param  array{title: string, type: string, content: string|null, order_no: int, published: bool}  $data
     */
    public function execute(int $moduleId, array $data): Lesson
    {
        return Lesson::create([
            'module_id' => $moduleId,
            'title' => $data['title'],
            'type' => $data['type'],
            'content' => $data['content'] ?? null,
            'order_no' => $data['order_no'],
            'published_at' => $data['published'] ? now() : null,
        ]);
    }
}
