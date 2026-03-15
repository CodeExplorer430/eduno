<?php

declare(strict_types=1);

namespace App\Domain\Module\Actions;

use App\Domain\Module\Models\Module;

class UpdateModule
{
    public function handle(Module $module, array $data): Module
    {
        $module->title = $data['title'];
        $module->description = $data['description'] ?? null;

        if (isset($data['order_no'])) {
            $module->order_no = (int) $data['order_no'];
        }

        $module->save();

        return $module;
    }
}
