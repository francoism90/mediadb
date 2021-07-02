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
            'views' => $this->views,
            'avatar_url' => $this->whenAppended('avatar_url'),
            'roles' => $this->whenAppended('assigned_roles'),
            'permissions' => $this->whenAppended('assigned_permissions'),
            'settings' => $this->whenAppended('settings'),
            'created_at' => $this->created_at,
        ];
    }
}
