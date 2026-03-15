<?php

declare(strict_types=1);

namespace App\Domain\Module\Actions;

use App\Domain\Module\Models\Module;
use Illuminate\Support\Facades\Log;

class PublishModule
{
    public function handle(Module $module): Module
    {
        if ($module->published_at !== null) {
            Log::info('Module unpublished', ['module_id' => $module->id]);
            $module->published_at = null;
        } else {
            Log::info('Module published', ['module_id' => $module->id]);
            $module->published_at = now();
        }

        $module->save();

        return $module;
    }
}
