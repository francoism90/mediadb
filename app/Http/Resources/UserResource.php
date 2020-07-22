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
            'thumbnail_url' => $this->whenAppended('thumbnail_url'),
            'relationships' => [
                'channels' => MediaResource::collection($this->whenLoaded('channels')),
            ],
        ];
    }
}
