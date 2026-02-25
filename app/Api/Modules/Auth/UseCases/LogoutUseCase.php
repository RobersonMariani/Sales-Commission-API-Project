<?php

namespace App\Api\Modules\Auth\UseCases;

class LogoutUseCase
{
    public function execute(): void
    {
        auth('api')->logout();
    }
}
