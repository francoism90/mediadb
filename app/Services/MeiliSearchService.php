<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Laravel\Scout\Builder;
use MeiliSearch\Endpoints\Indexes;
use RuntimeException;

class MeiliSearchService
{
    public ?Model $subject = null;

    public ?Request $request = null;

    public ?array $options = [];

    public ?array $scopes = [];

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

    public function filter(string $key, mixed $value = null, string $expression = 'OR'): static
    {
        $value = is_string($value) ? explode(',', $value) : $value;

        $values = collect($value)
            ->map(fn ($item) => sprintf('%s = "%s"', $key, $item))
            ->implode(sprintf(' %s ', $expression));

        $this->add(
            'filter',
            $values
        );

        return $this;
    }

    public function scopes(mixed $scopes): static
    {
        throw_if(!$this->subject, RuntimeException::class, 'A subject needs to be given.');

        $scopes = is_string($scopes) ? explode(',', $scopes) : $scopes;

        $ids = $this->subject->scopes($scopes)->get()->pluck('id');

        $this->filter('id', $ids);

        return $this;
    }

    public function get(): Collection
    {
        return $this
            ->engine()
            ->get();
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
