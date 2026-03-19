<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Accessibility\Actions\UpdatePreferences;
use App\Http\Requests\UpdatePreferencesRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PreferencesController extends Controller
{
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Accessibility', [
            'preferences' => $request->user()?->preferences,
        ]);
    }

    public function update(UpdatePreferencesRequest $request, UpdatePreferences $action): RedirectResponse
    {
        $action->handle($request->user(), $request->validated());

        return redirect()->back()->with('status', 'preferences-updated');
    }
}
