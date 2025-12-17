<?php

namespace App\Http\Resources;

use App\Utils\Resources\BaseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends BaseResource
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
            'user' => UserResource::make($this->whenLoaded('user')),
            'manager' => ManagerResource::make($this->whenLoaded('manager')),
            'chat' => ChatResource::make($this->whenLoaded('chat')),
        ];
        return array_merge($attributes, $customFields);
    }
}
