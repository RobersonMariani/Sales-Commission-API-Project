<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\UseCases;

class LogoutUseCase
{
    public function execute(): void
    {
        auth('api')->logout();
    }
}
