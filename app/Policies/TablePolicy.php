<?php

namespace App\Policies;

use App\Models\meja_billiard;
use App\Models\User;

/**
 * TablePolicy
 *
 * Handles authorization for meja_billiard (Table) model operations
 * admin and super_admin can view tables
 * Only super_admin can create/update/delete tables
 */
class TablePolicy
{
    /**
     * View any tables - check table.view permission
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('table.view');
    }

    /**
     * View a table - check table.view permission
     */
    public function view(User $user, meja_billiard $table): bool
    {
        return $user->hasPermissionTo('table.view');
    }

    /**
     * Create table - check table.create permission
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('table.create');
    }

    /**
     * Update table - check table.update permission
     */
    public function update(User $user, meja_billiard $table): bool
    {
        return $user->hasPermissionTo('table.update');
    }

    /**
     * Delete table - check table.delete permission
     */
    public function delete(User $user, meja_billiard $table): bool
    {
        return $user->hasRole('super_admin');
    }
}
