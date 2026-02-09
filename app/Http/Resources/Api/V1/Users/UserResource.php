<?php

namespace App\Http\Resources\Api\V1\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'message'      => __('auth.success'),
            'access_token' => $this->createToken('auth_token')->plainTextToken,
            'token_type'   => 'Bearer',
            'user'         => [
                'id'    => $this->id,
                'name'  => $this->name,
                'email' => $this->email,
                // Agrega aquí los campos que necesites
            ],
        ];
    }
}
