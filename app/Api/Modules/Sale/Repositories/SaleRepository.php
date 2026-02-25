<?php

namespace App\Api\Modules\Sale\Repositories;

use App\Api\Modules\Sale\Data\SaleQueryData;
use App\Models\Sale;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SaleRepository
{
    public function create(array $data): Sale
    {
        return Sale::query()->create($data);
    }

    public function findById(int $id): ?Sale
    {
        return Sale::query()->with('seller:id,name,email')->find($id);
    }

    /**
     * @return LengthAwarePaginator<int, Sale>
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
