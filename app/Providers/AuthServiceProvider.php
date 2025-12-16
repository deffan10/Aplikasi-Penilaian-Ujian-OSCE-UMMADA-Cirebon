<?php

namespace App\Providers;

use App\Models\Stasi;
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
        // Gate for admin-only access
        Gate::define('admin-only', function (User $user) {
            return $user->isAdmin();
        });

        // Gate for penguji to assess a specific stasi
        Gate::define('menilai-stasi', function (User $user, Stasi $stasi) {
            // Admin can do anything
            if ($user->isAdmin()) {
                return true;
            }
            
            // Penguji can only assess assigned stasi
            return $user->assignedStasi()
                ->whereKey($stasi->id)
                ->wherePivot('aktif', true)
                ->exists();
        });

        // Gate for viewing stasi (read-only for all authenticated users)
        Gate::define('lihat-stasi', function (User $user, Stasi $stasi) {
            return true; // All authenticated users can view
        });
    }
}
