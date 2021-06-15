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
        if ($this->input('query')) {
            // Overwrite QueryBuilder with ScoutBuilder
            $this->query = forward_static_call(
                [$this->getModel(), 'search'], $this->input('query')
            );
        }

        return parent::handle();
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
        // return $this->where('id', 3);

        return $this->orderBy('name', $this->input('direction', 'desc'));
    }
}
