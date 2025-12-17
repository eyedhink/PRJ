<?php

namespace App\Http\Resources;

use App\Utils\Resources\BaseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends BaseResource
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
            'role' => RoleResource::make($this->whenLoaded('role')),
            'tasks' => TaskResource::collection($this->whenLoaded('tasks')),
            'reports' => ReportResource::collection($this->whenLoaded('reports')),
        ];
        return array_merge($attributes, $customFields);
    }
}
