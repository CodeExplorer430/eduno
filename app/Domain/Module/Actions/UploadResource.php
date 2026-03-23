<?php

declare(strict_types=1);

namespace App\Domain\Module\Actions;

use App\Domain\Module\Models\Lesson;
use App\Domain\Module\Models\Resource;
use App\Enums\ResourceVisibility;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UploadResource
{
    /**
     * @param  array{accessibility_notes?: string|null, reading_time_minutes?: int|null}  $metadata
     */
    public function handle(
        Lesson $lesson,
        UploadedFile $file,
        string $title,
        ResourceVisibility $visibility,
        array $metadata = []
    ): Resource {
        $filename = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();

        $module = $lesson->module;
        $path = $file->storeAs(
            "resources/{$module->course_section_id}/{$module->id}/{$lesson->id}",
            $filename,
            'private'
        );

        return DB::transaction(function () use ($lesson, $title, $path, $file, $visibility, $metadata): Resource {
            return Resource::create([
                'lesson_id'            => $lesson->id,
                'title'                => $title,
                'file_path'            => (string) $path,
                'mime_type'            => $file->getMimeType() ?? 'application/octet-stream',
                'size_bytes'           => $file->getSize(),
                'visibility'           => $visibility,
                'accessibility_notes'  => $metadata['accessibility_notes'] ?? null,
                'reading_time_minutes' => $metadata['reading_time_minutes'] ?? null,
            ]);
        });
    }
}
