<?php

namespace App\Http\Resources;

use App\Utils\Resources\BaseResource;
use Illuminate\Http\Request;
class TaskResource extends BaseResource
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
            'tasked' => UserResource::make($this->whenLoaded('tasked')),
            'tasker' => ManagerResource::make($this->whenLoaded('tasker')),
        ];
        return array_merge($attributes, $customFields);
    }
}
