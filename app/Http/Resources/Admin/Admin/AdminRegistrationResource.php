<?php

namespace App\Http\Resources\Admin\Admin;

use App\Models\AdminRegistration;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AdminRegistration
 */
class AdminRegistrationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'email' => $this->email,
            'token' => $this->token,
            'details' => $this->details,
            'expiration' => $this->expiration,
            'sent_at' => $this->sent_at,
            'created_at' => $this->created_at,
        ];
    }
}
