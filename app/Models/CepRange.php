<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CepRange extends Model
{
    protected $table = 'cep_ranges';

    protected $fillable = [
        'uf',
        'cidade',
        'cep_de',
        'cep_ate',
        'tipo',
    ];

    protected $casts = [
        'cep_de' => 'string',
        'cep_ate' => 'string',
    ];

    /**
     * Busca cidade e UF por CEP
     *
     * @param  string  $cep  CEP com ou sem máscara
     */
    public static function findByCep(string $cep): ?self
    {
        // Remove formatação do CEP
        $cep = preg_replace('/\D/', '', $cep);

        if (strlen($cep) !== 8) {
            return null;
        }

        // Busca CEP na faixa
        return self::where('cep_de', '<=', $cep)
            ->where('cep_ate', '>=', $cep)
            ->first();
    }

    /**
     * Busca todas as cidades de um estado
     *
     * @param  string  $uf  Unidade Federativa (2 caracteres)
     */
    public static function findByUf(string $uf): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('uf', strtoupper($uf))
            ->distinct()
            ->orderBy('cidade')
            ->get(['cidade', 'uf']);
    }

    /**
     * Busca cidades por nome (busca parcial)
     *
     * @param  string  $cidade  Nome da cidade
     */
    public static function findByCity(string $cidade): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('cidade', 'like', "%{$cidade}%")
            ->distinct()
            ->orderBy('cidade')
            ->get(['cidade', 'uf']);
    }

    /**
     * Valida se um CEP existe na base
     *
     * @param  string  $cep  CEP com ou sem máscara
     */
    public static function validateCep(string $cep): bool
    {
        return self::findByCep($cep) !== null;
    }

    /**
     * Scope para buscar por faixa de CEP
     */
    public function scopeInRange(Builder $query, string $cep): Builder
    {
        $cep = preg_replace('/\D/', '', $cep);

        return $query->where('cep_de', '<=', $cep)
            ->where('cep_ate', '>=', $cep);
    }

    /**
     * Scope para buscar por UF
     */
    public function scopeByUf(Builder $query, string $uf): Builder
    {
        return $query->where('uf', strtoupper($uf));
    }

    /**
     * Scope para buscar por cidade
     */
    public function scopeByCity(Builder $query, string $cidade): Builder
    {
        return $query->where('cidade', 'like', "%{$cidade}%");
    }
}
