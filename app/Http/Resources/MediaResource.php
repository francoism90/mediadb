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
            'locale' => $this->locale,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'bitrate' => $this->whenAppended('bitrate'),
            'codec_name' => $this->whenAppended('codec_name'),
            'duration' => $this->whenAppended('duration'),
            'resolution' => $this->whenAppended('resolution'),
            'height' => $this->whenAppended('height'),
            'width' => $this->whenAppended('width'),
        ];
    }
}
