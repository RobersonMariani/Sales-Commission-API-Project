<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\UseCases;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Caso de uso que retorna os dados do usuário autenticado.
 */
class GetMeUseCase
{
    /**
     * Obtém o usuário atualmente autenticado via JWT.
     */
    public function execute(): ?Authenticatable
    {
        return auth('api')->user();
    }
}
