<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePreferencesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'font_size' => ['required', 'in:small,medium,large'],
            'high_contrast' => ['boolean'],
            'reduced_motion' => ['boolean'],
            'simplified_layout' => ['boolean'],
        ];
    }
}
