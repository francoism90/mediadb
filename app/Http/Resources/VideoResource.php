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
            'season_number' => $this->season_number,
            'episode_number' => $this->episode_number,
            'poster_url' => $this->poster_url,
            'type' => $this->type,
            'duration' => $this->duration,
            'quality' => $this->quality,
            'released_at' => $this->released_at,
            'clip' => $this->whenAppended('clip', new MediaResource($this->clip)),
            'clips' => $this->whenAppended('clips', new MediaCollection($this->clips)),
            'favorite' => $this->whenAppended('favorite'),
            'following' => $this->whenAppended('following'),
            'overview' => $this->whenAppended('overview'),
            'dash_url' => $this->whenAppended('dash_url'),
            'sprite_url' => $this->whenAppended('sprite_url'),
            'status' => $this->whenAppended('status'),
            'type' => $this->whenAppended('type'),
            'views' => $this->whenAppended('views'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'model' => new ModelResource($this->whenLoaded('model')),
            'tags' => new TagCollection($this->whenLoaded('tags')),
        ];
    }
}
