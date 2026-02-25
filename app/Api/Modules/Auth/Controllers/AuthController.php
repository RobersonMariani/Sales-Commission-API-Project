<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\Controllers;

use App\Api\Modules\Auth\Data\LoginData;
use App\Api\Modules\Auth\Data\RegisterData;
use App\Api\Modules\Auth\Resources\AuthResource;
use App\Api\Modules\Auth\Resources\UserResource;
use App\Api\Modules\Auth\UseCases\GetMeUseCase;
use App\Api\Modules\Auth\UseCases\LoginUseCase;
use App\Api\Modules\Auth\UseCases\LogoutUseCase;
use App\Api\Modules\Auth\UseCases\RegisterUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controlador responsável pelas operações de autenticação.
 */
class AuthController extends Controller
{
    /**
     * Registra um novo usuário no sistema.
     */
    public function register(Request $request, RegisterUseCase $useCase): Response
    {
        $data = RegisterData::validateAndCreate($request->all());

        return UserResource::make($useCase->execute($data))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Realiza o login e retorna o token JWT.
     */
    public function login(Request $request, LoginUseCase $useCase): AuthResource
    {
        $data = LoginData::validateAndCreate($request->all());

        return AuthResource::make($useCase->execute($data));
    }

    /**
     * Realiza o logout do usuário autenticado, invalidando o token.
     */
    public function logout(LogoutUseCase $useCase): JsonResponse
    {
        $useCase->execute();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Retorna os dados do usuário autenticado.
     */
    public function me(GetMeUseCase $useCase): Response
    {
        $user = $useCase->execute();

        return UserResource::make($user)
            ->response()
            ->setStatusCode(200);
    }
}
