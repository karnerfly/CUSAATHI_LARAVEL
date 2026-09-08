<?php

namespace App\Http\Resources\Admin\Admin;

use App\Models\AdminRegistrationCampaign;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

/**
 * @mixin AdminRegistrationCampaign
 */
class AdminRegistrationCampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $api_url = URL::temporarySignedRoute('api.admin.registration', $this->expires_at, [
            'cmpid' => $this->id,
        ]);

        $parameters = [];
        $q = parse_url($api_url, PHP_URL_QUERY);
        parse_str($q, $parameters);

        return [
            'id' => $this->id,
            'active' => $this->active,
            'expires_at' => $this->expires_at,
            'api_url' => $api_url,
            'parameters' => $parameters,
            'admin' => $this->whenLoaded('admin', function () {
                return [
                    'id' => $this->admin->id,
                    'name' => $this->admin->name,
                    'email' => $this->admin->email,
                ];
            }),
            'registrations' => AdminRegistrationResource::collection($this->whenLoaded('registrations')),
            'created_at' => $this->created_at,
        ];
    }
}
