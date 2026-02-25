<?php

declare(strict_types=1);

namespace App\Api\Modules\Seller\UseCases;

use App\Api\Modules\Seller\Data\CreateSellerData;
use App\Api\Modules\Seller\Repositories\SellerRepository;
use App\Models\Seller;
use Illuminate\Support\Facades\DB;

class CreateSellerUseCase
{
    public function __construct(
        private readonly SellerRepository $repository,
    ) {}

    public function execute(CreateSellerData $data): Seller
    {
        return DB::transaction(function () use ($data) {
            return $this->repository->create($data->toArrayModel());
        });
    }
}
