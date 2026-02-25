<?php

declare(strict_types=1);

namespace App\Api\Modules\Sale\UseCases;

use App\Api\Modules\Sale\Data\CreateSaleData;
use App\Api\Modules\Sale\Repositories\SaleRepository;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

/**
 * Caso de uso responsável pela criação de uma nova venda.
 */
class CreateSaleUseCase
{
    /** Taxa de comissão aplicada sobre o valor da venda (8,5%). */
    private const COMMISSION_RATE = 0.085;

    public function __construct(
        private readonly SaleRepository $repository,
    ) {}

    /**
     * Cria uma venda calculando a comissão automaticamente dentro de uma transação.
     */
    public function execute(CreateSaleData $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $commission = round($data->value * self::COMMISSION_RATE, 2);
            $modelData = array_merge($data->toArrayModel(), ['commission' => $commission]);

            $sale = $this->repository->create($modelData);

            return $sale->load('seller:id,name,email');
        });
    }
}
