<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->getRouteKey(),
            'name' => $this->name,
            'mime_type' => $this->mime_type,
            'type' => $this->fileType,
            'size' => $this->whenAppended('size'),
            'properties' => $this->whenAppended('properties'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
