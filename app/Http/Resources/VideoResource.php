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
            'release_date' => $this->release_date,
            'season_number' => $this->season_number,
            'episode_number' => $this->episode_number,
            'views' => $this->views,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_favorited' => $this->whenAppended('is_favorited'),
            'is_liked' => $this->whenAppended('is_liked'),
            'overview' => $this->whenAppended('overview'),
            'tracks' => $this->whenAppended('tracks', MediaResource::collection($this->tracks)),
            'bitrate' => $this->whenAppended('bitrate'),
            'codec_name' => $this->whenAppended('codec_name'),
            'duration' => $this->whenAppended('duration'),
            'height' => $this->whenAppended('height'),
            'width' => $this->whenAppended('width'),
            'status' => $this->whenAppended('status'),
            'sprite' => $this->whenAppended('sprite'),
            'sprite_url' => $this->whenAppended('sprite_url'),
            'stream_url' => $this->whenAppended('stream_url'),
            'thumbnail_url' => $this->whenAppended('thumbnail_url'),
            'relationships' => [
                'model' => new ModelResource($this->whenLoaded('model')),
                'collections' => CollectionResource::collection($this->whenLoaded('collections')),
                'tags' => TagResource::collection($this->whenLoaded('tags')),
            ],
        ];
    }
}
