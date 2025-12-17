<?php

namespace App\Http\Resources;

use App\Utils\Resources\BaseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $attributes = parent::toArray($request);
        $customFields = [
            'master' => RoleResource::make($this->whenLoaded('master')),
            'slaves' => RoleResource::collection($this->whenLoaded('slaves')),
        ];
        return array_merge($attributes, $customFields);
    }
}
