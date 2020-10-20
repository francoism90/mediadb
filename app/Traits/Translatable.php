<?php

namespace App\Traits;

trait Translatable
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $value
     * @param string|null                           $locale
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereTranslation(
        $query,
        string $column,
        string $value,
        ?string $locale = null
    ) {
        $useLocale = $locale ?? locale();

        return $query->where("{$column}->{$useLocale}", $value);
    }
}
