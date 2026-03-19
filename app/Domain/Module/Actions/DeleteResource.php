<?php

declare(strict_types=1);

namespace App\Domain\Module\Actions;

use App\Domain\Module\Models\Resource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteResource
{
    public function handle(Resource $resource): void
    {
        DB::transaction(function () use ($resource): void {
            Storage::disk('private')->delete($resource->file_path);
            $resource->delete();
        });
    }
}
