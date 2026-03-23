<?php

namespace Modules\Bible\App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class IndexVersesRequest extends FormRequest
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
            'chapter_id' => ['nullable', 'integer', 'min:1'],
            'book_id' => ['nullable', 'integer', 'min:1'],
            'chapter_number' => ['nullable', 'integer', 'min:1'],
            'verse_range' => ['nullable', 'string', 'max:100', 'regex:/^[\d\s,\-]+$/'],
        ];
    }
}
