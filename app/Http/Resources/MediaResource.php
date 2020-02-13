<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
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
            'original_name' => $this->file_name,
            'status' => $this->status,
            'mimetype' => $this->mime_type,
            'description' => $this->description,
            'thumbnail' => $this->thumbnail,
            'preview' => $this->preview,
            'views' => $this->views,
            'collection' => $this->collection_name,
            'properties' => $this->custom_properties,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'relationships' => [
                'tags' => TagResource::collection($this->whenLoaded('tags')),
                'user' => new UserResource($this->whenLoaded('model')),
            ],
        ];
    }
}
