<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CollectionResource extends JsonResource
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
            'placeholder' => $this->placeholder_url,
            'media' => $this->media()->count(),
            'views' => $this->views,
            'created_at' => $this->created_at,
            'relationships' => [
                'media' => MediaResource::collection($this->whenLoaded('media')),
                'tags' => TagResource::collection($this->whenLoaded('tags')),
                'user' => new UserResource($this->whenLoaded('user')),
            ],
        ];
    }
}
