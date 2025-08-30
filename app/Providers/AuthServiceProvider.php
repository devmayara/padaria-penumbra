<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Gate para verificar se o usuário é admin
        Gate::define('isAdmin', function (User $user) {
            return $user->role === 'admin';
        });

        // Gate para verificar se o usuário é member
        Gate::define('isMember', function (User $user) {
            return $user->role === 'member';
        });

        // Gate para verificar se o usuário está ativo
        Gate::define('isActive', function (User $user) {
            return $user->is_active;
        });
    }
}
