<?php

declare(strict_types=1);

namespace App\Api\Modules\Sale\Repositories;

use App\Api\Modules\Sale\Data\SaleQueryData;
use App\Models\Sale;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Repositório responsável pelo acesso a dados de vendas.
 */
class SaleRepository
{
    /**
     * Persiste uma nova venda no banco de dados.
     */
    public function create(array $data): Sale
    {
        return Sale::query()->create($data);
    }

    /**
     * Busca uma venda pelo ID com o vendedor carregado.
     */
    public function findById(int $id): ?Sale
    {
        return Sale::query()->with('seller:id,name,email')->find($id);
    }

    /**
     * Retorna as vendas paginadas com filtro opcional por vendedor.
     */
    public function getAllPaginated(SaleQueryData $query): LengthAwarePaginator
    {
        $page = $query->page ?? 1;
        $perPage = $query->perPage ?? 15;

        return Sale::query()
            ->with('seller:id,name,email')
            ->when($query->sellerId !== null, fn ($q) => $q->where('seller_id', $query->sellerId))
            ->orderBy('sale_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(perPage: $perPage, page: $page);
    }
}
