<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Ressource Eloquent pour représenter la réponse de connexion (Login).
 */
class LoginResource extends JsonResource
{
    /**
     * Disable wrapping for this resource.
     */
    public static $wrap = null;

    /**
     * Transformer la ressource en tableau.
     */
    public function toArray(Request $request): array
    {
        return [
            'access_token' => $this->resource['access_token'],
            'token_type' => $this->resource['token_type'],
            'user' => new UserResource($this->resource['user']),
        ];
    }
}
