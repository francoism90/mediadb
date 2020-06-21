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
            'thumbnail' => $this->thumbnail,
            'views' => $this->views,
            'created_at' => $this->created_at,
            'relationships' => [
                'channels' => MediaResource::collection($this->whenLoaded('channels')),
            ],
        ];
    }
}
