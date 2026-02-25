<?php

declare(strict_types=1);

namespace App\Api\Modules\Seller\UseCases;

use App\Api\Modules\Sale\Jobs\SendDailySellerCommissionJob;
use App\Api\Modules\Seller\Repositories\SellerRepository;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Caso de uso responsável pelo reenvio do e-mail de comissão de um vendedor.
 */
class ResendCommissionUseCase
{
    public function __construct(
        private readonly SellerRepository $repository,
    ) {}

    /**
     * Despacha o job de envio do e-mail de comissão para o vendedor na data informada.
     *
     * @throws HttpResponseException Quando o vendedor não é encontrado.
     */
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
