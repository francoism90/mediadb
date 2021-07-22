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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'favorite' => $this->whenAppended('favorite'),
            'following' => $this->whenAppended('following'),
            'status' => $this->whenAppended('status'),
            'overview' => $this->whenAppended('overview'),
            'thumbnail_url' => $this->whenAppended('thumbnail_url'),
            'vod_url' => $this->whenAppended('vod_url'),
            'views' => $this->whenAppended('views', $this->viewersCount()),
            'clip' => $this->whenAppended('clip', new MediaResource($this->clip)),
            'captions' => $this->whenAppended('captions', MediaResource::collection($this->captions)),
            'model' => new ModelResource($this->whenLoaded('model')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
