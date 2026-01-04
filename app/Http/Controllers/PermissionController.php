<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Show list of users for selecting which one to manage permissions
     *
     * Only displays:
     * - Admin users
     * - Excludes: super_admin, kitchen, and currently logged-in user
     */
    public function selectUser()
    {
        // Check role.view permission to manage permissions
        if (! auth()->user()->hasPermissionTo('role.view')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengakses fitur ini.');
        }

        // Get users with 'admin' role only (exclude super_admin, kitchen, and current user)
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })
            ->where('id', '!=', auth()->id())
            ->orderBy('name')
            ->paginate(15);

        return view('admin.manage-permissions.select-user', compact('users'));
    }

    /**
     * Show manage permissions page for selected user
     */
    public function managePermissions($userId)
    {
        // Check role.view permission to manage permissions
        if (! auth()->user()->hasPermissionTo('role.view')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengakses fitur ini.');
        }

        // Find the user
        $user = User::findOrFail($userId);

        // Don't allow managing super_admin permissions
        if ($user->hasRole('super_admin')) {
            return redirect()->route('admin.permissions.select-user')
                ->with('error', 'Tidak bisa mengelola permissions super admin.');
        }

        // Get all permissions grouped by category (prefix sebelum dot) - optimized
        $allPermissions = Permission::select('id', 'name', 'guard_name')->get();

        // Group permissions by prefix (order, payment, kitchen, etc)
        $groupedPermissions = [];
        foreach ($allPermissions as $permission) {
            $parts = explode('.', $permission->name);
            $category = $parts[0] ?? 'other';

            if (! isset($groupedPermissions[$category])) {
                $groupedPermissions[$category] = [];
            }

            $groupedPermissions[$category][] = $permission;
        }

        // Get user's current permissions
        $userPermissions = $user->getDirectPermissions()->pluck('name')->toArray();

        return view('admin.manage-permissions.manage', [
            'user' => $user,
            'groupedPermissions' => $groupedPermissions,
            'userPermissions' => $userPermissions,
        ]);
    }

    /**
     * Update user permissions
     */
    public function updatePermissions(Request $request, $userId)
    {
        // Check role.assign permission to assign permissions
        if (! auth()->user()->hasPermissionTo('role.assign')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengakses fitur ini.',
            ], 403);
        }

        $user = User::findOrFail($userId);

        // Don't allow managing super_admin permissions
        if ($user->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa mengelola permissions super admin.',
            ], 403);
        }

        // Get selected permissions from request
        $selectedPermissions = $request->get('permissions', []);

        // Validate that selected permissions exist
        $validPermissions = Permission::whereIn('name', $selectedPermissions)->pluck('name')->toArray();

        try {
            // Remove all direct permissions and assign new ones
            $user->syncPermissions($validPermissions);

            // CRITICAL: Clear all cache layers
            // 1. Clear Spatie permission cache
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            // 2. Refresh the user instance to reload permissions
            $user->refresh();

            // Set flash message untuk session
            session()->flash('success', 'Permissions berhasil diupdate untuk user '.$user->name.'.');

            return response()->json([
                'success' => true,
                'message' => 'Permissions berhasil diupdate untuk user '.$user->name.'.',
                'redirect_url' => route('admin.permissions.select-user'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle single permission for user (via AJAX)
     */
    public function togglePermission(Request $request, $userId)
    {
        // Check role.assign permission to assign permissions
        if (! auth()->user()->hasPermissionTo('role.assign')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengakses fitur ini.',
            ], 403);
        }

        $request->validate([
            'permission' => 'required|string|exists:permissions,name',
        ]);

        $user = User::findOrFail($userId);

        // Don't allow managing super_admin permissions
        if ($user->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa mengelola permissions super admin.',
            ], 403);
        }

        $permission = $request->get('permission');

        try {
            if ($user->hasPermissionTo($permission)) {
                // Revoke permission
                $user->revokePermissionTo($permission);
                $status = 'revoked';
                $message = 'Permission "'.$permission.'" berhasil dicabut.';
            } else {
                // Give permission
                $user->givePermissionTo($permission);
                $status = 'granted';
                $message = 'Permission "'.$permission.'" berhasil diberikan.';
            }

            // CRITICAL: Clear all cache layers
            // 1. Clear Spatie permission cache
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            // 2. Refresh the user instance
            $user->refresh();

            return response()->json([
                'success' => true,
                'message' => $message,
                'status' => $status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }
}
