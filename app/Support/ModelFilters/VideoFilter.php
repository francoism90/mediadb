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

        $this->query->simplePaginate(
            $this->getModel()->getPerPage(),
            ['*'],
            'page',
            $this->input('page', 1)
        );

        logger($this->query->toSql());

        /** @var \Laravel\Scout\Builder */
        $this->query = $this->getModel()->search($this->input('query', ''));

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

        if ($this->input('query') || (!empty($this->query->orders) || !empty($this->query->unionOrders))) {
            return $this;
        }

        return $this->inRandomSeedOrder();
    }

    public function sortByName()
    {
        return $this->orderBy('name->en', $this->input('direction', 'asc'));
    }
}
