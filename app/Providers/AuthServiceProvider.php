<?php

namespace App\Providers;

use App\Models\Collection;
use App\Models\Media;
use App\Models\Tag;
use App\Models\User;
use App\Models\Video;
use App\Policies\CollectionPolicy;
use App\Policies\MediaPolicy;
use App\Policies\TagPolicy;
use App\Policies\UserPolicy;
use App\Policies\VideoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Collection::class => CollectionPolicy::class,
        Media::class => MediaPolicy::class,
        Tag::class => TagPolicy::class,
        User::class => UserPolicy::class,
        Video::class => VideoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(fn ($user, $ability) => $user->hasRole('super-admin') ? true : null);
    }
}
