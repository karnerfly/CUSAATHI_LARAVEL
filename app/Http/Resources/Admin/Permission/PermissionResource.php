<?php

namespace App\Http\Resources\Admin\Permission;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @mixin Permission
 */
class PermissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'ability' => $this->ability,
            'category' => $this->category,
            'active' => (bool) $this->pivot->active,
            'assigned_at' => $this->pivot->created_at ? Carbon::parse($this->pivot->created_at) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
