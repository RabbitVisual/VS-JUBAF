<?php

namespace Modules\Bible\App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class IndexBooksRequest extends FormRequest
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
            'version_id' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
