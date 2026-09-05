<?php

namespace App\Http\Resources\Admin\College;

use App\Models\College\Stream;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Stream
 */
class StreamResource extends JsonResource
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
            'slug' => $this->slug,
            'course' => CourseResource::make($this->whenLoaded('course')),
        ];
    }
}
