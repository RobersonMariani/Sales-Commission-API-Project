<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\UseCases;

/**
 * Caso de uso responsável pelo logout do usuário.
 */
class LogoutUseCase
{
    /**
     * Invalida o token JWT do usuário autenticado.
     */
    public function execute(): void
    {
        auth('api')->logout();
    }
}
