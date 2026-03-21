<?php

declare(strict_types=1);

namespace App\Http\Requests\Module;

use Illuminate\Foundation\Http\FormRequest;

class CreateModuleRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'order_no' => ['required', 'integer', 'min:0'],
            'published' => ['boolean'],
        ];
    }
}
