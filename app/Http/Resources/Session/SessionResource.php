<?php

namespace App\Http\Resources\Session;

use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Jenssegers\Agent\Agent;

/**
 * @mixin Session
 *
 * @property bool $current
 */
class SessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $agent = new Agent();
        $agent->setUserAgent($this->user_agent);

        return [
            'id' => $this->id,
            'ip_address' => $this->ip_address,
            'device' => $agent->device(),
            'platform' => $agent->platform(),
            'browser' => $agent->browser(),
            'current' => $this->current,
            'last_active' => Carbon::createFromTimestamp($this->last_activity)->diffForHumans(),
        ];
    }
}
