<?php

namespace App\Http\Resources\Admin\College;

use App\Models\College\College;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin College
 */
class CollegeDetailResource extends JsonResource
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
            'description' => $this->description,
            'type' => $this->type,
            'slug' => $this->slug,
            'thumbnail_url' => $this->thumbnail_url,
            'website_url' => $this->website_url,
            'established_year' => $this->established_year,
            'accreditation_body' => $this->accreditation_body,
            'accreditation_grade' => $this->accreditation_grade,
            'accreditation_year' => $this->accreditation_year,
            'accreditation_value' => $this->accreditation_value,
            'contact_details' => $this->contact_details,
            'location' => CollegeLocationResource::make($this->whenLoaded('location')),
            'images' => CollegeImageResource::collection($this->whenLoaded('images')),
            'facilities' => FacilityResource::collection($this->whenLoaded('facilities')),
            'verified_at' => $this->verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
