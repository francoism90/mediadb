<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait Channelable
{
    /**
     * @param array  $items
     * @param string $status
     *
     * @return Collection
     */
    public function firstOrCreateChannels(array $items = [], string $status = 'published'): Collection
    {
        $collection = collect();

        foreach ($items as $item) {
            $model = $this->channels()->firstOrCreate(
                ['name' => $item['name']]
            );

            if (!$model->status()) {
                $model->setStatus($status);
            }

            $collection->push($model);
        }

        return $collection;
    }
}
