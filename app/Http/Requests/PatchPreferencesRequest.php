<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatchPreferencesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'reduced_motion'      => ['sometimes', 'boolean'],
            'high_contrast'       => ['sometimes', 'boolean'],
            'dyslexia_font'       => ['sometimes', 'boolean'],
            'simplified_layout'   => ['sometimes', 'boolean'],
            'dark_mode'           => ['sometimes', 'boolean'],
            'font_size'           => ['sometimes', 'string', 'in:small,medium,large'],
            'email_notifications' => ['sometimes', 'boolean'],
            'email_digest'        => ['sometimes', 'boolean'],
        ];
    }
}
