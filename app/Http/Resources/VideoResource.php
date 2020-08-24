<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
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
            'type' => $this->type,
            'status' => $this->status,
            'views' => $this->views,
            'release_date' => $this->release_date,
            'original_language' => $this->original_language,
            'original_title' => $this->original_title,
            'season_number' => $this->season_number,
            'episode_number' => $this->episode_number,
            'tagline' => $this->tagline,
            'overview' => $this->overview,
            'metadata' => $this->whenAppended('metadata'),
            'tracks' => $this->whenAppended('tracks', MediaResource::collection($this->tracks)),
            'preview_url' => $this->whenAppended('preview_url'),
            'sprite_url' => $this->whenAppended('sprite_url'),
            'stream_url' => $this->whenAppended('stream_url'),
            'thumbnail_url' => $this->whenAppended('thumbnail_url'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'relationships' => [
                'model' => new UserResource($this->whenLoaded('model')),
                'collections' => CollectionResource::collection($this->whenLoaded('collection')),
                'tags' => TagResource::collection($this->whenLoaded('tags')),
            ],
        ];
    }
}
