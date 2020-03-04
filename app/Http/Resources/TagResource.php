<?php

namespace App\Http\Resources;

use App\Models\Collection;
use App\Models\Media;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
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
            'placeholder' => $this->placeholder_url,
            'collect' => $this->when(
                $this->hasAppend('collect'),
                $this->getTagCountByType(Collection::class)
            ),
            'media' => $this->when(
                $this->hasAppend('media'),
                $this->getTagCountByType(Media::class)
            ),
            'views' => $this->views,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
