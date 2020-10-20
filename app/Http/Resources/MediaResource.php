<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    /**
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'download_url' => $this->download_url,
            'collection' => $this->collection,
            'locale' => $this->locale,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
