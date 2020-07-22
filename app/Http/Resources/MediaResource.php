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
            'views' => $this->views,
            'collection' => $this->collection_name,
            'properties' => $this->custom_properties,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'download_url' => $this->whenAppended('download_url'),
            'preview_url' => $this->whenAppended('preview_url'),
            'sprite_url' => $this->whenAppended('sprite_url'),
            'stream_url' => $this->whenAppended('stream_url'),
            'thumbnail_url' => $this->whenAppended('thumbnail_url'),
            'relationships' => [
                'model' => new ChannelResource($this->whenLoaded('model')),
                'tags' => TagResource::collection($this->whenLoaded('tags')),
            ],
        ];
    }
}
