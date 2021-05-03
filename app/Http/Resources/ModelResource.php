<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ModelResource extends JsonResource
{
    /**
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->getRouteKey(),
            'name' => $this->name,
            'favorite' => $this->favorite,
            'type' => $this->getTable(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
