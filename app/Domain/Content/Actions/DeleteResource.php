<?php

declare(strict_types=1);

namespace App\Domain\Content\Actions;

use App\Domain\Content\Models\Resource;
use Illuminate\Support\Facades\Storage;

class DeleteResource
{
    public function execute(Resource $resource): void
    {
        Storage::disk('private')->delete($resource->file_path);
        $resource->delete();
    }
}
