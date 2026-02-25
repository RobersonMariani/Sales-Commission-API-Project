<?php

declare(strict_types=1);

namespace App\Api\Modules\Seller\UseCases;

use App\Api\Modules\Seller\Repositories\SellerRepository;
use App\Models\Seller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GetSellerUseCase
{
    public function __construct(
        private readonly SellerRepository $repository,
    ) {}

    public function execute(int $id): Seller
    {
        $seller = $this->repository->findById($id);

        if ($seller === null) {
            throw new ModelNotFoundException('Seller not found.');
        }

        return $seller;
    }
}
