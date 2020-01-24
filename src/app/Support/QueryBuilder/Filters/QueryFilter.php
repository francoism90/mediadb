<?php

namespace App\Support\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\Filters\Filter;

class QueryFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        // Array's should not be processed
        $value = is_array($value) ? implode(' ', $value) : $value;

        // Sanitize input
        $input = $this->sanitize((string) $value);
        $input = $this->process($value);

        // Return empty record on invalid input
        if (!$input || $input === 'null' || $input === '*') {
            return $query->where('id', 0);
        }

        // Get queried models
        $models = $this->getQueryModels($query->getModel(), $input);

        $ids = $models->pluck('id')->toArray();
        $idsOrder = implode(',', $ids);

        return $query->whereIn('id', $ids)
                     ->orderByRaw(DB::raw("FIELD(id, $idsOrder)"));
    }

    /**
     * @param string $str
     *
     * @return string
     */
    private function sanitize(string $str = ''): string
    {
        $str = filter_var(
            $str,
            FILTER_SANITIZE_STRING,
            FILTER_FLAG_NO_ENCODE_QUOTES |
            FILTER_FLAG_STRIP_LOW |
            FILTER_FLAG_STRIP_HIGH
        );

        return preg_replace('/\s+/', ' ', trim($str));
    }

    /**
     * @param string $str
     *
     * @return string
     */
    private function process(string $str = ''): string
    {
        $query = [];

        // Get all quoted words
        preg_match('/".*?"/', $str, $quoted);

        foreach ($quoted as $part) {
            $query[] = $part;
        }

        // Process each word
        foreach (explode(' ', $str) as $part) {
            $strAfter = Str::after($part, $part[0]);

            if ($this->startsWithOperator($part) && ctype_alnum($strAfter)) {
                $query[] = $part;
            } elseif (ctype_alnum($part)) {
                $query[] = "*{$part}*";
            }
        }

        return implode(' ', $query);
    }

    /**
     * @param string $str
     *
     * @return bool
     */
    private function startsWithOperator(string $str): bool
    {
        return Str::startsWith($str, ['+', '-', '@', '<', '>', '~', '*']);
    }

    /**
     * @param Model  $model
     * @param string $str
     *
     * @return void
     */
    private function getQueryModels(Model $model, string $str = '*')
    {
        return $model->search($str)
            ->select(['name', 'description'])
            ->collapse('id')
            ->from(0)
            ->take(10000)
            ->get();
    }
}
