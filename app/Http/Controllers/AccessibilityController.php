<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Accessibility\Actions\UpdateUserPreferences;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccessibilityController extends Controller
{
    public function update(Request $request, UpdateUserPreferences $action): RedirectResponse
    {
        $validated = $request->validate([
            'reduced_motion' => ['sometimes', 'boolean'],
            'high_contrast' => ['sometimes', 'boolean'],
            'dyslexia_font' => ['sometimes', 'boolean'],
            'font_size' => ['sometimes', 'string', 'in:small,medium,large'],
        ]);

        /** @var User $user */
        $user = $request->user();

        $action->execute($user, $validated);

        return back();
    }
}
