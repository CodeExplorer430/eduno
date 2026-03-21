<?php

declare(strict_types=1);

namespace App\Domain\Accessibility\Actions;

use App\Domain\Accessibility\Models\UserPreference;
use App\Domain\Audit\Actions\LogAction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SaveAccessibilityPreferences
{
    public function __construct(private readonly LogAction $logAction)
    {
    }

    public function execute(User $user, string $fontSize, bool $highContrast, bool $reducedMotion, bool $simplifiedLayout, string $language): UserPreference
    {
        return DB::transaction(function () use ($user, $fontSize, $highContrast, $reducedMotion, $simplifiedLayout, $language): UserPreference {
            /** @var UserPreference $preferences */
            $preferences = UserPreference::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'font_size' => $fontSize,
                    'high_contrast' => $highContrast,
                    'reduced_motion' => $reducedMotion,
                    'simplified_layout' => $simplifiedLayout,
                    'language' => $language,
                ]
            );

            $this->logAction->execute(
                $user->id,
                'accessibility.preferences_updated',
                UserPreference::class,
                $preferences->id,
                [
                    'font_size' => $fontSize,
                    'high_contrast' => $highContrast,
                    'reduced_motion' => $reducedMotion,
                    'simplified_layout' => $simplifiedLayout,
                    'language' => $language,
                ]
            );

            return $preferences;
        });
    }
}
