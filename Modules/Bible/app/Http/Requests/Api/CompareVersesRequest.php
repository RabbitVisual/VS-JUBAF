<?php

namespace Modules\Bible\App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CompareVersesRequest extends FormRequest
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
            'v1' => ['required', 'integer', 'min:1'],
            'v2' => ['required', 'integer', 'min:1'],
            'book_number' => ['required', 'integer', 'min:1'],
            'chapter' => ['required', 'integer', 'min:1'],
            'verse' => ['required', 'integer', 'min:1'],
        ];
    }
}
