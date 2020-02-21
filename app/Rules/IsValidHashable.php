<?php

namespace App\Rules;

use App\Traits\Hashidable;
use Illuminate\Contracts\Validation\Rule;

class IsValidHashable implements Rule
{
    /**
     * @var string
     */
    public $model;

    public function __construct(string $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Hashidable::getModelByKey($value, $this->model)->exists();
    }

    /**
     * @return string
     */
    public function message()
    {
        return 'A non existing model has been given.';
    }
}
