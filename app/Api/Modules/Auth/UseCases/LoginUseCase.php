<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\UseCases;

use App\Api\Modules\Auth\Data\LoginData;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Response;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;

class LoginUseCase
{
    /**
     * @return array{token: string, token_type: string, expires_in: int}
     */
    public function execute(LoginData $data): array
    {
        /** @var JWTGuard $guard */
        $guard = auth('api');

        $token = $guard->attempt($data->toCredentials());

        if (! $token) {
            throw new HttpResponseException(
                Response::json(['message' => 'Invalid credentials'], 401),
            );
        }

        $ttl = $guard->factory()->getTTL();
        $expiresIn = (int) ($ttl * 60);

        return [
            'token' => (string) $token,
            'token_type' => 'bearer',
            'expires_in' => $expiresIn,
        ];
    }
}
