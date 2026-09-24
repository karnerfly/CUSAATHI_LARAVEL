<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Permission;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
    public function boot(UrlGenerator $url): void
    {
        if (app()->environment('production')) {
            $url->forceScheme('https');
        }

        if (!app()->runningInConsole() && Schema::hasTable('permissions')) {
            Permission::all()->each(function ($permission) {
                Gate::define($permission->ability, function ($u) use ($permission) {
                    if ($u instanceof Admin) {
                        return $u->hasAbility($permission->ability);
                    }

                    return false;
                });
            });
        }

        JsonResource::withoutWrapping();

        Route::bind('admin', function ($value) {
            return Admin::query()
                ->whereKey($value)
                ->whereKeyNot(Auth::guard('admin')->id())
                ->firstOrFail();
        });
    }
}
