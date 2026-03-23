<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CepService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CepController extends Controller
{
    protected CepService $cepService;

    public function __construct(CepService $cepService)
    {
        $this->cepService = $cepService;
    }

    /**
     * Busca endereço por CEP
     */
    public function buscar(Request $request): JsonResponse
    {
        $request->validate([
            'cep' => 'required|string|min:8|max:10',
        ]);

        $cep = $request->input('cep');
        $endereco = $this->cepService->buscarPorCep($cep);

        if (! $endereco) {
            return response()->json([
                'success' => false,
                'message' => 'CEP não encontrado',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $endereco,
        ]);
    }

    /**
     * Valida CEP
     */
    public function validar(Request $request): JsonResponse
    {
        $request->validate([
            'cep' => 'required|string|min:8|max:10',
        ]);

        $cep = $request->input('cep');
        $valido = $this->cepService->validarCep($cep);

        return response()->json([
            'success' => true,
            'valido' => $valido,
        ]);
    }

    /**
     * Busca cidades por UF (2 letras, ex.: SP, RJ)
     */
    public function cidadesPorUf(string $uf): JsonResponse
    {
        $uf = strtoupper(trim($uf));
        if (strlen($uf) !== 2 || ! ctype_alpha($uf)) {
            return response()->json([
                'success' => false,
                'message' => 'UF inválida. Informe a sigla com 2 letras (ex.: SP).',
            ], 422);
        }
        $cidades = $this->cepService->buscarCidadesPorUf($uf);

        return response()->json([
            'success' => true,
            'data' => $cidades,
        ]);
    }

    /**
     * Busca cidades por nome
     */
    public function cidadesPorNome(Request $request): JsonResponse
    {
        $request->validate([
            'cidade' => 'required|string|min:3',
        ]);

        $cidade = $request->input('cidade');
        $cidades = $this->cepService->buscarCidadesPorNome($cidade);

        return response()->json([
            'success' => true,
            'data' => $cidades,
        ]);
    }
}
