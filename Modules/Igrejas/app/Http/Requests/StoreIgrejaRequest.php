<?php

namespace Modules\Igrejas\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIgrejaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('gerenciar igrejas') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'pastor_titular' => ['nullable', 'string', 'max:255'],
            'lideranca_titular' => ['nullable', 'string', 'max:255'],
            'lider_jovens' => ['nullable', 'string', 'max:255'],
            'cidade' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'string', 'size:2'],
            'logo_path' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}

