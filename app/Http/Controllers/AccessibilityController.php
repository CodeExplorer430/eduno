<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Accessibility\Actions\UpdatePreferences;
use App\Http\Requests\PatchPreferencesRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class AccessibilityController extends Controller
{
    public function update(PatchPreferencesRequest $request, UpdatePreferences $action): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $action->handle($user, $request->validated());

        return back();
    }
}
