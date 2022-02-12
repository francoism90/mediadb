<?php

namespace App\Actions\User;

use App\Events\User\UserHasBeenUpdated;
use App\Models\User;

class UpdateVideoDetails
{
    public function __invoke(User $user, array $data): void
    {
        $collect = collect($data);

        // Update attributes
        $locale = $collect->get('locale', $user->preferredLocale());

        $user->extra_attributes
            ->set('locale', $collect->get('locale', $locale));

        $user->saveOrFail();

        // Dispatch event
        UserHasBeenUpdated::dispatch(
            $user->refresh()
        );
    }
}
