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
            'avatar_url' => $this->whenAppended('avatar_url'),
            'email' => $this->whenAppended('email'),
            'permissions' => $this->whenAppended('assigned_permissions'),
            'roles' => $this->whenAppended('assigned_roles'),
            'settings' => $this->whenAppended('settings'),
            'created_at' => $this->created_at,
        ];
    }
}
