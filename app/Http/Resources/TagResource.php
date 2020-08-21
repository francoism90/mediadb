<?php

namespace App\Http\Resources;

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
            'item_count' => $this->whenAppended('item_count'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
