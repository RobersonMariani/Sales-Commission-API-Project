<?php

namespace App\Api\Modules\Auth\UseCases;

use Illuminate\Contracts\Auth\Authenticatable;

class GetMeUseCase
{
    public function execute(): ?Authenticatable
    {
        return auth('api')->user();
    }
}
