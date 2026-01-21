<?php

declare(strict_types = 1);

namespace App\Providers;

use App\Enums\Can;
use App\Models\User;
use App\Observers\UserObserver;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\Facades\LogViewer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->setupLogViewer();
        $this->configModels();
        $this->configObservers();
        $this->configCommands();
        $this->configUrls();
        $this->configDates();
        $this->configGates();
    }

    private function setupLogViewer(): void
    {
        // Setup authentication in /logs route
        LogViewer::auth(function ($request): ?bool {
            $user = $request->user();

            if (! $user) {
                return false;
            }

            // Super admins always have access
            if ($user->is_admin) {
                return true;
            }

            // Check view-logs permission
            return $user->hasPermission('view-logs');
        });
    }

    private function configModels(): void
    {
        // Remove the need of the property fillable
        Model::unguard();

        // Make sure that all properties being called exists
        Model::shouldBeStrict();

        //
        Model::automaticallyEagerLoadRelationships();
    }

    private function configObservers(): void
    {
        User::observe(UserObserver::class);
    }

    private function configCommands(): void
    {
        DB::prohibitDestructiveCommands(
            app()->isProduction()
        );
    }

    private function configUrls(): void
    {
        // Force HTTPS in production OR when APP_URL uses HTTPS (ngrok)
        $shouldForceHttps = app()->isProduction() || str_starts_with((string) config('app.url'), 'https://');

        URL::forceHttps($shouldForceHttps);
    }

    private function configDates(): void
    {
        Date::use(CarbonImmutable::class);
    }

    private function configGates(): void
    {
        foreach (Can::cases() as $permission) {
            Gate::define(
                $permission->value,
                function ($user) use ($permission): bool {
                    // Super admins bypass all checks
                    if ($user->is_admin) {
                        return true;
                    }

                    // Check direct permissions
                    if ($user->permissions()->whereName($permission->value)->exists()) {
                        return true;
                    }

                    // Check permissions through roles
                    return $user->roles()
                        ->whereHas(
                            'permissions',
                            fn ($query) => $query->whereName($permission->value)
                        )
                        ->exists();
                }
            );
        }
    }
}
