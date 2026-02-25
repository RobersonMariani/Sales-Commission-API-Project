<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource que formata a resposta de autenticação com token JWT.
 */
class AuthResource extends JsonResource
{
    public function __construct(array $resource)
    {
        parent::__construct($resource);
    }

    /**
     * Transforma o recurso de autenticação em array para a resposta JSON.
     */
    public function toArray(Request $request): array
    {
        return [
            'token' => $this->resource['token'],
            'token_type' => $this->resource['token_type'],
            'expires_in' => $this->resource['expires_in'],
        ];
    }
}
