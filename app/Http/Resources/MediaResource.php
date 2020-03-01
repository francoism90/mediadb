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
            'placeholder' => $this->placeholder_url,
            'preview' => $this->preview_url,
            'views' => $this->views,
            'collection' => $this->collection_name,
            'properties' => $this->custom_properties,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'download' => $this->when($this->hasField('download_url'), $this->download_url),
            'stream' => $this->when($this->hasField('stream_url'), $this->stream_url),
            'usercollect' => $this->when(
                $this->hasField('collections'),
                CollectionResource::collection($this->user_collections)
            ),
            'relationships' => [
                'collect' => CollectionResource::collection($this->whenLoaded('collections')),
                'tags' => TagResource::collection($this->whenLoaded('tags')),
                'user' => new UserResource($this->whenLoaded('model')),
            ],
        ];
    }
}
