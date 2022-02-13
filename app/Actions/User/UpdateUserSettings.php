<?php

namespace App\Actions\User;

use App\Events\User\UserHasBeenUpdated;
use App\Models\User;

class UpdateUserSettings
{
    public function __invoke(User $user, array $data): void
    {
        $value = fn (string $key, mixed $default = null) => data_get($data, $key, $default);

        // Update attributes
        $locale = $value('locale', $user->preferredLocale());

        $user->extra_attributes
            ->set('locale', $value('locale', $locale));

        $user->saveOrFail();

        // Dispatch event
        UserHasBeenUpdated::dispatch(
            $user->refresh()
        );
    }
}
