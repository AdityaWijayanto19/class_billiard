<?php

namespace App\Providers;

use App\Models\CategoryMenu;
use App\Models\meja_billiard;
use App\Models\Menu;
use App\Models\orders;
use App\Models\payments;
use App\Models\User;
use App\Policies\CategoryMenuPolicy;
use App\Policies\MenuPolicy;
use App\Policies\OrderPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\TablePolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        orders::class => OrderPolicy::class,
        payments::class => PaymentPolicy::class,
        Menu::class => MenuPolicy::class,
        User::class => UserPolicy::class,
        CategoryMenu::class => CategoryMenuPolicy::class,
        meja_billiard::class => TablePolicy::class,
    ];

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
        // Auto-create storage symlink if not exists (for production hosting)
        $this->ensureStorageLink();

        // Register policies
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        // Safe Blade directive to avoid throwing PermissionDoesNotExist
        // Use in blade as: @hasPermissionSafe('permission.name') ... @endhasPermissionSafe
        Blade::if('hasPermissionSafe', function ($permission) {
            try {
                /** @var \App\Models\User|null $user */
                $user = Auth::user();
                if (! $user) {
                    return false;
                }

                return $user->hasPermissionTo($permission);
            } catch (\Exception $e) {
                // On any unexpected error, fail safe (do not render protected block)
                return false;
            }
        });
    }

    /**
     * Ensure storage symlink exists for production hosting (LiteSpeed/shared hosting)
     * This creates public/storage -> storage/app/public symlink automatically
     */
    private function ensureStorageLink(): void
    {
        $link = public_path('storage');
        $target = storage_path('app/public');

        // Only create if symlink doesn't exist and we're not in console (avoid issues during artisan commands)
        if (! file_exists($link) && ! app()->runningInConsole()) {
            try {
                // Try to create symlink
                if (function_exists('symlink')) {
                    @symlink($target, $link);
                    Log::info('Storage symlink created automatically');
                }
            } catch (\Exception $e) {
                // Silently fail - hosting might not support symlinks
                Log::warning('Failed to create storage symlink: ' . $e->getMessage());
            }
        }
    }
}
