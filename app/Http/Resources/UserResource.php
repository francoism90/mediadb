<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->getRouteKey(),
            'slug' => $this->slug,
            'name' => $this->name,
            'views' => $this->views,
            'created_at' => $this->created_at,
            'roles' => $this->whenAppended('assigned_roles'),
            'thumbnail_url' => $this->whenAppended('thumbnail_url'),
        ];
    }
}
