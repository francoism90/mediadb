<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->getRouteKey(),
            'slug' => $this->slug,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'avatar_url' => $this->whenAppended('avatar_url'),
            'permissions' => $this->whenAppended('assigned_permissions'),
            'roles' => $this->whenAppended('assigned_roles'),
            'settings' => $this->whenAppended('settings'),
        ];
    }
}
