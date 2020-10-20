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
            'type' => 'videos',
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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'overview' => $this->whenAppended('overview'),
            'bitrate' => $this->whenAppended('bitrate'),
            'codec_name' => $this->whenAppended('codec_name'),
            'duration' => $this->whenAppended('duration'),
            'height' => $this->whenAppended('height'),
            'width' => $this->whenAppended('width'),
            'sprite' => $this->whenAppended('sprite'),
            'sprite_url' => $this->whenAppended('sprite_url'),
            'stream_url' => $this->whenAppended('stream_url'),
            'thumbnail_url' => $this->whenAppended('thumbnail_url'),
            'titles' => $this->whenAppended('titles', CollectionResource::collection($this->titles)),
            'tracks' => $this->whenAppended('tracks', MediaResource::collection($this->tracks)),
            'relationships' => [
                'model' => new UserResource($this->whenLoaded('model')),
                'collections' => CollectionResource::collection($this->whenLoaded('collection')),
                'tags' => TagResource::collection($this->whenLoaded('tags')),
            ],
        ];
    }
}
