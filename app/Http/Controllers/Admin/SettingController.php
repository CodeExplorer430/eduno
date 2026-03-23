<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Audit\Actions\LogAction;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function __construct(private readonly LogAction $logAction)
    {
    }

    public function index(Request $request): Response
    {
        abort_unless($request->user()->isAdmin(), 403);

        /** @var array<string, array<string, string|null>> $settings */
        $settings = [];
        foreach (Setting::all() as $setting) {
            $settings[$setting->group][$setting->key] = $setting->value;
        }

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'site_name'                => ['sometimes', 'string', 'max:100'],
            'registration_open'        => ['sometimes', 'string', 'in:true,false'],
            'email_notifications'      => ['sometimes', 'string', 'in:true,false'],
            'deadline_reminder_hours'  => ['sometimes', 'integer', 'in:12,24,48'],
            'email_digest'             => ['sometimes', 'string', 'in:true,false'],
            'maintenance_mode'         => ['sometimes', 'string', 'in:true,false'],
        ]);

        $map = [
            'site_name'               => ['type' => 'string',  'group' => 'general'],
            'registration_open'       => ['type' => 'boolean', 'group' => 'general'],
            'maintenance_mode'        => ['type' => 'boolean', 'group' => 'general'],
            'email_notifications'     => ['type' => 'boolean', 'group' => 'notifications'],
            'deadline_reminder_hours' => ['type' => 'integer', 'group' => 'notifications'],
            'email_digest'            => ['type' => 'boolean', 'group' => 'notifications'],
        ];

        foreach ($validated as $key => $value) {
            $meta = $map[$key] ?? ['type' => 'string', 'group' => 'general'];
            Setting::set($key, (string) $value, $meta['type'], $meta['group']);
        }

        $this->logAction->execute(
            $request->user()->id,
            'admin.settings.updated',
            Setting::class,
            null,
            ['keys' => array_keys($validated)],
        );

        return redirect()->route('admin.settings.index')->with('success', 'Settings saved.');
    }
}
