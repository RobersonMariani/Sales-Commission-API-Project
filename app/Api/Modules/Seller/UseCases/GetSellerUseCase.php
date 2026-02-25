<?php

declare(strict_types=1);

namespace App\Api\Modules\Seller\UseCases;

use App\Api\Modules\Seller\Repositories\SellerRepository;
use App\Models\Seller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Caso de uso responsável pela busca de um vendedor específico.
 */
class GetSellerUseCase
{
    public function __construct(
        private readonly SellerRepository $repository,
    ) {}

    /**
     * Busca um vendedor pelo ID ou lança exceção se não encontrado.
     *
     * @throws ModelNotFoundException Quando o vendedor não é encontrado.
     */
    public function execute(int $id): Seller
    {
        $seller = $this->repository->findById($id);

        if ($seller === null) {
            throw new ModelNotFoundException('Seller not found.');
        }

        return $seller;
    }
}
