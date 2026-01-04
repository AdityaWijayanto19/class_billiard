<?php

namespace App\Policies;

use App\Models\Menu;
use App\Models\User;

/**
 * MenuPolicy
 *
 * Model-level authorization untuk Menu
 * Digunakan untuk: $user->can('update', $menu)
 */
class MenuPolicy
{
    /**
     * View menus
     */
    public function viewAny(User $user): bool
    {
        return $user->can('menu.view');
    }

    /**
     * View specific menu
     */
    public function view(User $user, Menu $menu): bool
    {
        return $user->can('menu.view');
    }

    /**
     * Create menu
     */
    public function create(User $user): bool
    {
        return $user->can('menu.create');
    }

    /**
     * Update menu
     */
    public function update(User $user, Menu $menu): bool
    {
        return $user->can('menu.update');
    }

    /**
     * Delete menu
     */
    public function delete(User $user, Menu $menu): bool
    {
        return $user->can('menu.delete');
    }

    /**
     * Update price (super_admin only)
     */
    public function updatePrice(User $user, Menu $menu): bool
    {
        return $user->can('menu.update_price');
    }

    /**
     * Toggle availability
     */
    public function toggleAvailability(User $user, Menu $menu): bool
    {
        return $user->can('menu.toggle_availability');
    }

    /**
     * View categories
     */
    public function viewCategories(User $user): bool
    {
        return $user->can('menu.view_categories');
    }
}
