<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\UseCases;

use App\Api\Modules\Auth\Data\RegisterData;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Caso de uso responsável pelo registro de novos usuários.
 */
class RegisterUseCase
{
    /**
     * Cria um novo usuário no banco de dados dentro de uma transação.
     */
    public function execute(RegisterData $data): User
    {
        return DB::transaction(function () use ($data) {
            return User::query()->create($data->toArrayModel());
        });
    }
}
