<?php

namespace App\Http\Resources\Admin\College;

use App\Models\College\Image;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Image
 */
class CollegeImageResource extends JsonResource
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
            'group' => $this->group,
            'url' => $this->url,
            'alt_text' => $this->alt_text,
            'created_at' => $this->created_at,
        ];
    }
}
