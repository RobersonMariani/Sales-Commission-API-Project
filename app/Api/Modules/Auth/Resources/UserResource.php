<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource que formata os dados do usuário para a resposta JSON.
 *
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * Transforma o recurso de usuário em array para a resposta JSON.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
