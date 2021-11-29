<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Scout\Builder;
use MeiliSearch\Endpoints\Indexes;
use RuntimeException;

class MeiliSearchService
{
    public ?Model $subject = null;

    public ?Request $request = null;

    public ?array $options = [];

    public function engine(): Builder
    {
        throw_if(!$this->subject, RuntimeException::class, 'A subject needs to be given.');

        return $this->subject->search('', function (Indexes $meilisearch, $query, $options) {
            $options = array_merge($options, $this->options);

            return $meilisearch->search($query, $options);
        });
    }

    public function subject(string $class): static
    {
        $this->subject = app($class);

        return $this;
    }

    public function for(Request $request): static
    {
        $this->request = $request;

        return $this;
    }

    public function add(string $key, mixed $value, ?bool $force = false): static
    {
        if ($value || $force) {
            $this->options = Arr::add($this->options, $key, $value);
        }

        return $this;
    }

    public function sort(mixed $value = null): static
    {
        $value = is_string($value) ? explode(',', $value) : $value;

        $this->add('sort', $value);

        return $this;
    }

    public function paginate(): Paginator
    {
        return $this
            ->engine()
            ->simplePaginate(24)
            ->appends($this->request->query());
    }
}
