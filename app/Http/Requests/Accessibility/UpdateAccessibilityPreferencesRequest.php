<?php

declare(strict_types=1);

namespace App\Http\Requests\Accessibility;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccessibilityPreferencesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'font_size' => ['required', 'in:small,medium,large,xlarge'],
            'high_contrast' => ['required', 'boolean'],
            'reduced_motion' => ['required', 'boolean'],
            'simplified_layout' => ['required', 'boolean'],
            'language' => ['required', 'in:en'],
        ];
    }
}
