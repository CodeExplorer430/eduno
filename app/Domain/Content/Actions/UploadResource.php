<?php

declare(strict_types=1);

namespace App\Domain\Content\Actions;

use App\Domain\Content\Models\Resource;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadResource
{
    public function execute(int $lessonId, string $title, UploadedFile $file, string $visibility): Resource
    {
        $filename = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
        $path = Storage::disk('private')->putFileAs('resources', $file, $filename);

        if ($path === false) {
            throw new \RuntimeException('Failed to store resource file.');
        }

        return Resource::create([
            'lesson_id' => $lessonId,
            'title' => $title,
            'file_path' => $path,
            'mime_type' => $file->getMimeType() ?? $file->getClientMimeType(),
            'size_bytes' => $file->getSize(),
            'visibility' => $visibility,
        ]);
    }
}
