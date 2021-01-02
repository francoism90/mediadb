<?php

namespace App\Http\Resources;

use App\Models\Collection;
use App\Models\Video;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'views' => $this->whenAppended('views', $this->views),
            'items' => $this->whenAppended('items', $this->item_count),
            'collections' => $this->whenAppended('collections', $this->getItemCountAttribute(Collection::class)),
            'videos' => $this->whenAppended('videos', $this->getItemCountAttribute(Video::class)),
        ];
    }
}
