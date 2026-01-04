<?php

namespace App\Policies;

use App\Models\CategoryMenu;
use App\Models\User;

/**
 * CategoryMenuPolicy
 *
 * Handles authorization for CategoryMenu model operations
 * Only admin and super_admin can manage categories
 */
class CategoryMenuPolicy
{
    /**
     * View any categories - check category.view permission
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('category.view');
    }

    /**
     * View a category - check category.view permission
     */
    public function view(User $user, CategoryMenu $category): bool
    {
        return $user->hasPermissionTo('category.view');
    }

    /**
     * Create category - check category.create permission
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('category.create');
    }

    /**
     * Update category - check category.update permission
     */
    public function update(User $user, CategoryMenu $category): bool
    {
        return $user->hasPermissionTo('category.update');
    }

    /**
     * Delete category - check category.delete permission
     */
    public function delete(User $user, CategoryMenu $category): bool
    {
        return $user->hasRole('super_admin');
    }
}
