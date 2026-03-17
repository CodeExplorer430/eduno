<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Domain\Accessibility\Actions\SaveAccessibilityPreferences;
use App\Http\Controllers\Controller;
use App\Http\Requests\Accessibility\UpdateAccessibilityPreferencesRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccessibilityController extends Controller
{
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Accessibility', [
            'preferences' => $request->user()?->preferences,
        ]);
    }

    public function update(
        UpdateAccessibilityPreferencesRequest $request,
        SaveAccessibilityPreferences $action
    ): RedirectResponse {
        $validated = $request->validated();

        $action->execute(
            $request->user(),
            $validated['font_size'],
            (bool) $validated['high_contrast'],
            (bool) $validated['reduced_motion'],
            (bool) $validated['simplified_layout'],
            $validated['language'],
        );

        return redirect()->route('profile.accessibility.edit')
            ->with('success', 'Accessibility preferences saved.');
    }
}
