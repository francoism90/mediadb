<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->getRouteKey(),
            'slug' => $this->slug,
            'name' => $this->name,
            'type' => $this->type,
            'season_number' => $this->season_number,
            'episode_number' => $this->episode_number,
            'release_date' => $this->release_date,
            'clip' => $this->whenAppended('clip', new MediaResource($this->clip)),
            'favorite' => $this->whenAppended('favorite'),
            'following' => $this->whenAppended('following'),
            'overview' => $this->whenAppended('overview'),
            'poster_url' => $this->whenAppended('poster_url'),
            // 'sprite_url' => $this->whenAppended('sprite_url'),
            'status' => $this->whenAppended('status'),
            'views' => $this->whenAppended('views'),
            'vod_url' => $this->whenAppended('vod_url'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'model' => new ModelResource($this->whenLoaded('model')),
            'tags' => new TagCollection($this->whenLoaded('tags')),
        ];
    }
}
