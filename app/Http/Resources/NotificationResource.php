<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->getRouteKey(),
            'title' => data_get($this->data, 'title'),
            'body' => data_get($this->data, 'body'),
            'overview' => data_get($this->data, 'overview'),
            'type' => data_get($this->data, 'type'),
            'read_at' => $this->read_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
