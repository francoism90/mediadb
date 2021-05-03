<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->getRouteKey(),
            'slug' => $this->slug,
            'name' => $this->name,
            'type' => $this->type,
            'release_date' => $this->release_date,
            'season_number' => $this->season_number,
            'episode_number' => $this->episode_number,
            'status' => $this->status,
            'views' => $this->views,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'favorite' => $this->whenAppended('favorite'),
            'overview' => $this->whenAppended('overview'),
            'clip' => $this->whenAppended('clip', new MediaResource($this->clip)),
            'tracks' => $this->whenAppended('tracks', MediaResource::collection($this->tracks)),
            'model' => new ModelResource($this->whenLoaded('model')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
