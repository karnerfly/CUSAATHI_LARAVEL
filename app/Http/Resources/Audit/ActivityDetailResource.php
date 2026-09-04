<?php

namespace App\Http\Resources\Audit;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Jenssegers\Agent\Agent;
use OwenIt\Auditing\Models\Audit;

/**
 * @mixin Audit
 */
class ActivityDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $agent = new Agent;
        $agent->setUserAgent($this->user_agent);

        return [
            'id' => $this->id,
            'event' => $this->event,
            'tags' => $this->tags,
            'url' => $this->url,

            'actor' => $this->user
                ? [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'type' => class_basename($this->actor_type),
                ]
                : null,

            'resource' => $this->auditable
                ? [
                    'id' => $this->auditable->id,
                    'type' => class_basename($this->auditable_type),
                ]
                : null,

            'changes' => [
                'old' => $this->old_values,
                'new' => $this->new_values,
            ],

            'client' => [
                'ip_address' => $this->ip_address,
                'device' => $agent->device(),
                'platform' => $agent->platform(),
                'browser' => $agent->browser(),
            ],

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
