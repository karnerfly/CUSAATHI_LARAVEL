<?php

namespace App\Http\Resources\Admin;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin NewsletterSubscriber
 */
class NewsletterSubscriberDetailResource extends JsonResource
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
            'user_id' => $this->user_id,
            'email' => $this->email,
            'active' => $this->active,
            'frequency' => $this->frequency,
            'verified_at' => $this->verified_at,
            'verification_token' => $this->verification_token,
            'unsubscribe_token' => $this->unsubscribe_token,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'user' => $this->when(
                $this->relationLoaded('user'),
                fn() => $this->user
                    ? [
                        'name' => $this->user->name,
                        'profile_url' => $this->user->profile_url,
                    ]
                    : null,
            ),

            'topic_ids' => $this->when($this->relationLoaded('topics'), fn() => $this->topics->pluck('id')->values()),
        ];
    }
}
