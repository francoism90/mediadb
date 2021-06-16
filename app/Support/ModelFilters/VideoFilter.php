<?php

namespace App\Support\ModelFilters;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Str;

class VideoFilter extends ModelFilter
{
    /**
     * @var array
     */
    public $relations = [];

    public function handle()
    {
        parent::handle();

        // Build MeiliSearch filters
        $filters = $this->query->get()->map(function ($model) {
            return 'id = '.$model->id;
        })->join(' OR ');

        /** @var \Laravel\Scout\Builder */
        $this->query = $this->getModel()->search(
            $this->input('query', ''),
            function ($engine, string $query, array $options) use ($filters) {
                $options['filters'] = $filters;

                return $engine->search($query, $options);
            }
        );

        return $this->query;
    }

    public function bookmarks()
    {
        return $this->withFavorites();
    }

    public function sort(?string $column = null)
    {
        if (method_exists($this, $method = 'sortBy'.Str::studly($column))) {
            return $this->$method();
        }

        if (!empty($this->query->orders) || !empty($this->query->unionOrders)) {
            return $this;
        }

        return $this->inRandomSeedOrder();
    }

    public function sortByName()
    {
        return $this->orderBy('name->en', $this->input('direction', 'asc'));
    }
}
