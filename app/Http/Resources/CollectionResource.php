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
            'overview' => $this->overview,
            'type' => $this->type,
            'views' => $this->views,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'status' => $this->whenAppended('status'),
            'is_subscribed' => $this->whenAppended('is_subscribed'),
            'thumbnail_url' => $this->whenAppended('thumbnail_url'),
            'item_count' => $this->whenAppended('item_count'),
            'relationships' => [
                'tags' => TagResource::collection($this->whenLoaded('tags')),
                'videos' => VideoResource::collection($this->whenLoaded('videos')),
            ],
        ];
    }
}
