<?php

namespace Modules\Comunicacao\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostagemRequest extends FormRequest
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
            'titulo' => ['required', 'string', 'max:255'],
            'conteudo' => ['required', 'string'],
            'tipo' => ['required', Rule::in(['edital', 'ata', 'aviso', 'noticia'])],
            'anexo' => ['nullable', 'file', 'max:20480'],
        ];
    }
}
