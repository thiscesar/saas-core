<?php

declare(strict_types = 1);

namespace App\Providers;

use App\Enums\Can;
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
        $this->configCommands();
        $this->configUrls();
        $this->configDates();
        $this->configGates();
    }

    private function setupLogViewer(): void
    {
        // Setup authentication in /logs route
        LogViewer::auth(fn ($request): ?bool => $request->user()->is_admin);
    }

    private function configModels(): void
    {
        // Remove the need of the property fillable
        Model::unguard();

        // Make sure that all properties being called exists
        Model::shouldBeStrict();
    }

    private function configCommands(): void
    {
        DB::prohibitDestructiveCommands(
            app()->isProduction()
        );
    }

    private function configUrls(): void
    {
        URL::forceHttps(
            app()->isProduction()
        );
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
                fn ($user): bool => $user
                    ->permissions()
                    ->whereName($permission->value)
                    ->exists()
            );
        }
    }
}
