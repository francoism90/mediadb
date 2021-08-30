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
            'kind' => $this->kind,
            'mime_type' => $this->mime_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'bitrate' => $this->whenAppended('bitrate'),
            'duration' => $this->whenAppended('duration'),
            'locale' => $this->whenAppended('locale'),
            'height' => $this->whenAppended('height'),
            'width' => $this->whenAppended('width'),
            'resolution' => $this->whenAppended('resolution'),
            'size' => $this->whenAppended('size'),
        ];
    }
}
