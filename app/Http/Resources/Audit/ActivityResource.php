<?php

namespace App\Http\Resources\Audit;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OwenIt\Auditing\Models\Audit;

/**
 * @mixin Audit
 */
class ActivityResource extends JsonResource
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
            'event' => $this->event,
            'actor' => $this->user
                ? [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'type' => class_basename($this->actor_type),
                ]
                : null,
            'resource' =>
                $this->auditable_id && $this->auditable_type
                    ? [
                        'id' => $this->auditable_id,
                        'type' => class_basename($this->auditable_type),
                    ]
                    : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
