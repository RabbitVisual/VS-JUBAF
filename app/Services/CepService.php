<?php

namespace App\Services;

use App\Models\CepRange;
use Illuminate\Support\Facades\Cache;

class CepService
{
    /**
     * Busca informações de endereço por CEP
     *
     * @param  string  $cep  CEP com ou sem máscara
     * @return array|null Retorna array com uf, cidade, cep ou null se não encontrado
     */
    public function buscarPorCep(string $cep): ?array
    {
        // Remove formatação
        $cep = preg_replace('/\D/', '', $cep);

        if (strlen($cep) !== 8) {
            return null;
        }

        // Cache por 24 horas
        $cacheKey = "cep_{$cep}";

        return Cache::remember($cacheKey, 86400, function () use ($cep) {
            $cepRange = CepRange::findByCep($cep);

            if (! $cepRange) {
                return null;
            }

            return [
                'cep' => self::formatarCep($cep),
                'cidade' => $cepRange->cidade,
                'uf' => $cepRange->uf,
                'cep_de' => self::formatarCep($cepRange->cep_de),
                'cep_ate' => self::formatarCep($cepRange->cep_ate),
                'tipo' => $cepRange->tipo,
            ];
        });
    }

    /**
     * Valida se um CEP existe
     *
     * @param  string  $cep  CEP com ou sem máscara
     */
    public function validarCep(string $cep): bool
    {
        $cep = preg_replace('/\D/', '', $cep);

        return strlen($cep) === 8 && CepRange::validateCep($cep);
    }

    /**
     * Busca cidades por UF
     *
     * @param  string  $uf  Unidade Federativa (2 caracteres)
     */
    public function buscarCidadesPorUf(string $uf): array
    {
        $uf = strtoupper($uf);
        $cacheKey = "cidades_uf_{$uf}";

        return Cache::remember($cacheKey, 86400, function () use ($uf) {
            $cidades = CepRange::findByUf($uf);

            return $cidades->map(function ($item) {
                return [
                    'cidade' => $item->cidade,
                    'uf' => $item->uf,
                ];
            })->unique('cidade')->values()->toArray();
        });
    }

    /**
     * Busca cidades por nome
     *
     * @param  string  $cidade  Nome da cidade
     */
    public function buscarCidadesPorNome(string $cidade): array
    {
        $cacheKey = 'cidades_nome_'.md5($cidade);

        return Cache::remember($cacheKey, 3600, function () use ($cidade) {
            $cidades = CepRange::findByCity($cidade);

            return $cidades->map(function ($item) {
                return [
                    'cidade' => $item->cidade,
                    'uf' => $item->uf,
                ];
            })->unique(function ($item) {
                return $item['cidade'].$item['uf'];
            })->values()->toArray();
        });
    }

    /**
     * Formata CEP com máscara (método estático)
     *
     * @param  string|null  $cep  CEP sem formatação
     * @return string CEP formatado (00000-000)
     */
    public static function formatar(?string $cep): string
    {
        if (empty($cep)) {
            return '';
        }

        $cep = preg_replace('/\D/', '', $cep);

        if (strlen($cep) !== 8) {
            return $cep;
        }

        return substr($cep, 0, 5).'-'.substr($cep, 5, 3);
    }

    /**
     * Formata CEP com máscara (método estático - alias para formatar)
     *
     * @param  string|null  $cep  CEP sem formatação
     * @return string CEP formatado (00000-000)
     */
    public static function formatarCep(?string $cep): string
    {
        return self::formatar($cep);
    }

    /**
     * Formata CPF com máscara
     *
     * @param  string|null  $cpf  CPF sem formatação
     * @return string CPF formatado (000.000.000-00)
     */
    public static function formatarCpf(?string $cpf): string
    {
        if (empty($cpf)) {
            return '';
        }

        $cpf = preg_replace('/\D/', '', $cpf);

        if (strlen($cpf) !== 11) {
            return $cpf;
        }

        return substr($cpf, 0, 3).'.'.substr($cpf, 3, 3).'.'.substr($cpf, 6, 3).'-'.substr($cpf, 9, 2);
    }

    /**
     * Formata telefone com máscara
     *
     * @param  string|null  $telefone  Telefone sem formatação
     * @return string Telefone formatado
     */
    public static function formatarTelefone(?string $telefone): string
    {
        if (empty($telefone)) {
            return '';
        }

        $telefone = preg_replace('/\D/', '', $telefone);

        if (strlen($telefone) === 10) {
            // Telefone fixo: (00) 0000-0000
            return '('.substr($telefone, 0, 2).') '.substr($telefone, 2, 4).'-'.substr($telefone, 6, 4);
        } elseif (strlen($telefone) === 11) {
            // Celular: (00) 9 0000-0000
            return '('.substr($telefone, 0, 2).') '.substr($telefone, 2, 1).' '.substr($telefone, 3, 4).'-'.substr($telefone, 7, 4);
        }

        return $telefone;
    }

    /**
     * Remove formatação do CEP
     *
     * @param  string  $cep  CEP com ou sem formatação
     * @return string CEP sem formatação
     */
    public function removerFormatacao(string $cep): string
    {
        return preg_replace('/\D/', '', $cep);
    }
}
