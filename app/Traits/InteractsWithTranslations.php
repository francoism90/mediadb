<?php

namespace App\Traits;

use Spatie\Translatable\HasTranslations as BaseHasTranslations;

trait InteractsWithTranslations
{
    use BaseHasTranslations;

    public function toArray()
    {
        $attributes = parent::toArray();

        foreach ($this->getTranslatableAttributes() as $field) {
            $attributes[$field] = $this->getTranslation($field, \App::getLocale());
        }

        return $attributes;
    }

    /**
     * @param string $field
     *
     * @return array
     */
    public function extractTranslations(string $field = 'name'): array
    {
        $translations = array_values($this->getTranslations($field));

        return array_unique($translations);
    }
}
