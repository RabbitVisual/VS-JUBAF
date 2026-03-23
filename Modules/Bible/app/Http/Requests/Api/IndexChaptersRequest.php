<?php

namespace Modules\Bible\App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class IndexChaptersRequest extends FormRequest
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
            'book_id' => ['nullable', 'integer', 'min:1'],
            'book_name' => ['nullable', 'string', 'max:100'],
            'version_id' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * Require either book_id or (book_name + version_id).
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('book_id')) {
                return;
            }
            if (! $this->filled('book_name') || ! $this->filled('version_id')) {
                $validator->errors()->add('book_id', 'O parâmetro book_id ou (book_name e version_id) é obrigatório.');
            }
        });
    }
}
