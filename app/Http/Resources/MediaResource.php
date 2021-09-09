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
            'type' => $this->type,
            'size' => $this->whenAppended('size'),
            'metadata' => $this->whenAppended('metadata'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
