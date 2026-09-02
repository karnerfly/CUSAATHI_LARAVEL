<?php

namespace App\Http\Resources\Admin;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin NewsletterSubscriber
 */
class NewsletterSubscriberResource extends JsonResource
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
            'verified_at' => $this->verified_at,
            'created_at' => $this->created_at,
        ];
    }
}
