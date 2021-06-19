<?php

namespace App\Traits;

trait InteractsWithTranslations
{
    public function extractTranslations(string $field = 'name'): array
    {
        $translations = array_values($this->getTranslations($field));

        return array_unique($translations);
    }
}
