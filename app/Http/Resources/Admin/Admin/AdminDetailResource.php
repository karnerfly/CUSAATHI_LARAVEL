<?php

namespace App\Http\Resources\Admin\Admin;

use App\Http\Resources\Admin\Permission\PermissionResource;
use App\Http\Resources\Session\SessionResource;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Admin
 */
class AdminDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'profile_url' => $this->profile_url,
            'active' => $this->active,
            'sessions' => SessionResource::collection($this->whenLoaded('sessions')),
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
