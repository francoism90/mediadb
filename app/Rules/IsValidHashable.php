<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Vinkla\Hashids\Facades\Hashids;

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
        $id = Hashids::connection($this->model)->decode($value)[0];

        return $this->model::exists($id);
    }

    /**
     * @return string
     */
    public function message()
    {
        return 'A non existing model has been given.';
    }
}
