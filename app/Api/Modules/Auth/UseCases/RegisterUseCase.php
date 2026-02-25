<?php

namespace App\Api\Modules\Auth\UseCases;

use App\Api\Modules\Auth\Data\RegisterData;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RegisterUseCase
{
    public function execute(RegisterData $data): User
    {
        return DB::transaction(function () use ($data) {
            return User::query()->create($data->toArrayModel());
        });
    }
}
