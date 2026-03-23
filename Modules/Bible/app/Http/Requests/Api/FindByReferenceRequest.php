<?php

namespace Modules\Bible\App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class FindByReferenceRequest extends FormRequest
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
            'ref' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'ref.required' => 'O parâmetro ref (referência) é obrigatório.',
        ];
    }
}
