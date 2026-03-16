<?php

declare(strict_types=1);

namespace App\Domain\Content\Actions;

use App\Domain\Content\Models\Module;

class UpdateModule
{
    /**
     * @param  array{title: string, description: string|null, order_no: int, published: bool}  $data
     */
    public function execute(Module $module, array $data): Module
    {
        $published = $data['published'];

        $module->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'order_no' => $data['order_no'],
            'published_at' => $published
                ? ($module->published_at ?? now())
                : null,
        ]);

        return $module;
    }
}
