<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ModelResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->getRouteKey(),
            'name' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'favorite' => $this->whenAppended('favorite'),
            'following' => $this->whenAppended('following'),
        ];
    }
}
