<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckPermission Middleware
 *
 * Enforce permission checks di route level
 *
 * Usage:
 *   Route::get('/admin/orders', [OrderController::class, 'index'])
 *       ->middleware('permission:order.view');
 *
 * Multiple permissions (OR logic):
 *   ->middleware('permission:order.view|order.create');
 *
 * Multiple permissions (AND logic):
 *   ->middleware('permission:order.view,order.create');
 */
class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        // User harus authenticated
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user has ANY of the required permissions (OR logic)
        foreach ($permissions as $permission) {
            // Handle multiple permissions separated by | (OR)
            if (str_contains($permission, '|')) {
                $perms = explode('|', $permission);
                if ($user->hasAnyPermission($perms)) {
                    return $next($request);
                }
            }
            // Handle single permission
            elseif ($user->can($permission)) {
                return $next($request);
            }
        }

        // Jika semua permission checks gagal
        abort(403, 'Unauthorized. You do not have the required permission.');
    }
}
