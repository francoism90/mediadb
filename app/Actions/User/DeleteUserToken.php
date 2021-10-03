<?php

namespace App\Actions\User;

use App\Models\User;

class DeleteUserToken
{
    public function __invoke(User $user, string $token): void
    {
        $user->tokens()?->firstWhere('token', $token)?->delete();
    }
}
