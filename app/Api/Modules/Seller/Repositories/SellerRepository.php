<?php

declare(strict_types=1);

namespace App\Api\Modules\Seller\Repositories;

use App\Api\Modules\Seller\Data\SellerQueryData;
use App\Models\Seller;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Repositório responsável pelo acesso a dados de vendedores.
 */
class SellerRepository
{
    /**
     * Persiste um novo vendedor no banco de dados.
     */
    public function create(array $data): Seller
    {
        return Seller::query()->create($data);
    }

    /**
     * Busca um vendedor pelo ID.
     */
    public function findById(int $id): ?Seller
    {
        return Seller::query()->find($id);
    }

    /**
     * Retorna os vendedores paginados ordenados por ID.
     */
    public function getAllPaginated(SellerQueryData $query): LengthAwarePaginator
    {
        $page = $query->page ?? 1;
        $perPage = $query->perPage ?? 15;

        return Seller::query()
            ->orderBy('id')
            ->paginate(perPage: $perPage, page: $page);
    }
}
