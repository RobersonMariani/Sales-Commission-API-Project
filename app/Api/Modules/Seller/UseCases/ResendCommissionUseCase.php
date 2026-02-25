<?php

declare(strict_types=1);

namespace App\Api\Modules\Seller\UseCases;

use App\Api\Modules\Sale\Jobs\SendDailySellerCommissionJob;
use App\Api\Modules\Seller\Repositories\SellerRepository;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResendCommissionUseCase
{
    public function __construct(
        private readonly SellerRepository $repository,
    ) {}

    public function execute(int $sellerId, ?string $date = null): void
    {
        $seller = $this->repository->findById($sellerId);

        if (! $seller) {
            throw new HttpResponseException(
                response()->json(['message' => 'Seller not found.'], 404),
            );
        }

        $date = $date ?? now()->toDateString();

        SendDailySellerCommissionJob::dispatch($seller, $date);
    }
}
