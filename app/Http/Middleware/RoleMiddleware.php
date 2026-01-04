<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Pastikan user sudah login
        if (! Auth::check()) {
            session()->put('url.intended', $request->fullUrl());

            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();

        // Super admin bisa akses semua role routes
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        // Parse role parameter (support "role1|role2" format)
        $allowedRoles = explode('|', $role);

        // Check spatie role (dengan fallback ke kolom role untuk backward compatibility)
        $userRole = $user->getRoleNames()->first() ?? $user->role;

        // Check if user's role is in allowed roles
        if (! in_array($userRole, $allowedRoles)) {
            // Format display roles
            $allowedRolesDisplay = array_map(function ($r) {
                return match ($r) {
                    'admin' => 'Admin',
                    'kitchen' => 'Kitchen',
                    'super_admin' => 'Super Admin',
                    default => ucfirst($r),
                };
            }, $allowedRoles);

            $userRoleDisplay = match ($userRole) {
                'admin' => 'Admin',
                'kitchen' => 'Kitchen',
                'super_admin' => 'Super Admin',
                default => ucfirst($userRole),
            };

            $rolesText = implode(', ', $allowedRolesDisplay);

            return redirect('/')->with('error', "Anda tidak memiliki akses. Halaman ini hanya untuk role: {$rolesText}. Role Anda: {$userRoleDisplay}");
        }

        return $next($request);
    }
}
