<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class MarkModelAsViewed
{
    public function execute(User $user, Model $model): void
    {
        $user->view($model);
    }
}
