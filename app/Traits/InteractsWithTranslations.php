<?php

namespace App\Traits;

trait InteractsWithTranslations
{
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
