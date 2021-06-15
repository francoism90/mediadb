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
        // Overwrite QueryBuilder with ScoutBuilder
        $this->query = forward_static_call(
            [$this->getModel(), 'search'], $this->input('query')
        );

        return parent::handle();
    }

    public function bookmarks($value)
    {
        // return $this->withFavorites();
        return $this;
    }

    public function sort($column)
    {
        if (method_exists($this, $method = 'sortBy'.Str::studly($column))) {
            return $this->$method();
        }

        return $this->orderBy('name', $this->input('direction', 'asc'));
    }

    public function sortByName()
    {
        return $this->orderBy('name->en', $this->input('direction', 'asc'));
    }

    public function sortByRecommended()
    {
        return $this;
    }
}
