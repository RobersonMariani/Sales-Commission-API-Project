<?php

namespace App\Api\Modules\Seller\Repositories;

use App\Api\Modules\Seller\Data\SellerQueryData;
use App\Models\Seller;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SellerRepository
{
    public function create(array $data): Seller
    {
        return Seller::query()->create($data);
    }

    public function findById(int $id): ?Seller
    {
        return Seller::query()->find($id);
    }

    /**
     * @return LengthAwarePaginator<int, Seller>
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
