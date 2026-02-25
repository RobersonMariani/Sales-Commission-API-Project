<?php

namespace App\Api\Modules\Auth\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * @param  array{token: string, token_type: string, expires_in: int}  $resource
     */
    public function __construct(array $resource)
    {
        parent::__construct($resource);
    }

    /**
     * @return array<string, mixed>
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
