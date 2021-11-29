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
            logger($options);

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

    public function getOption(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->options, $key, $default);
    }

    public function query(?string $value = null): static
    {
        $this->add('q', $value ?? '*');

        return $this;
    }

    public function sort(mixed $value = null): static
    {
        $value = is_string($value) ? explode(',', $value) : $value;

        $this->add('sort', $value);

        return $this;
    }

    public function limit(?int $value = null): static
    {
        $this->add('limit', $value ?? 20);

        return $this;
    }

    public function filter(string $key, mixed $value = null): static
    {
        $value = is_string($value) ? explode(',', $value) : $value;

        $values = collect($value)->map(fn ($item) => sprintf('%s = "%s"', $key, $item));
        logger($values);

        // $value = is_string($value) ? explode(',', $value) : $value;

        // $values = array_merge($this->getOption('filter'), $value);

        $this->add(
            'filter',
            $values->toArray()
        );

        return $this;
    }

    public function paginate(): Paginator
    {
        $perPage = $this->getOption('limit', 20);

        $query = $this->request?->query();

        return $this
            ->engine()
            ->simplePaginate($perPage)
            ->appends($query);
    }
}
