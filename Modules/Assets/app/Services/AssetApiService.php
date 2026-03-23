<?php

namespace Modules\Assets\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Assets\App\Models\Asset;

/**
 * Serviço central da API de patrimônio (v1).
 * Listagem paginada, busca por id e por código.
 */
class AssetApiService
{
    /**
     * Lista ativos com paginação.
     *
     * @return LengthAwarePaginator<Asset>
     */
    public function list(int $perPage = 20): LengthAwarePaginator
    {
        return Asset::with(['category', 'location'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Busca ativo por id. Retorna null se não encontrado.
     */
    public function getById(int $id): ?Asset
    {
        return Asset::with(['category', 'location'])->find($id);
    }

    /**
     * Busca ativo por código. Retorna null se não encontrado.
     */
    public function getByCode(string $code): ?Asset
    {
        return Asset::with(['category', 'location'])
            ->where('code', $code)
            ->first();
    }
}
