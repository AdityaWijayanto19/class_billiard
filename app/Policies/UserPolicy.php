<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Only super_admin can perform user management operations
     */
    private function isSuperAdmin(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine if the user can view any users (manage-users index)
     */
    public function viewAny(User $user): Response|bool
    {
        return $this->isSuperAdmin($user)
            ? Response::allow()
            : Response::deny('Hanya super_admin yang dapat mengakses manajemen pengguna.');
    }

    /**
     * Determine if the user can view a specific user
     */
    public function view(User $user, User $model): Response|bool
    {
        return $this->isSuperAdmin($user)
            ? Response::allow()
            : Response::deny('Hanya super_admin yang dapat melihat detail pengguna.');
    }

    /**
     * Determine if the user can create users
     */
    public function create(User $user): Response|bool
    {
        return $this->isSuperAdmin($user)
            ? Response::allow()
            : Response::deny('Hanya super_admin yang dapat membuat pengguna baru.');
    }

    /**
     * Determine if the user can update a user
     */
    public function update(User $user, User $model): Response|bool
    {
        // Super admin can edit any user except cannot downgrade themselves
        if (! $this->isSuperAdmin($user)) {
            return Response::deny('Hanya super_admin yang dapat mengedit pengguna.');
        }

        // Super admin cannot downgrade itself
        if ($user->id === $model->id) {
            return Response::deny('Anda tidak dapat mengubah role diri sendiri.');
        }

        return Response::allow();
    }

    /**
     * Determine if the user can delete a user
     */
    public function delete(User $user, User $model): Response|bool
    {
        // Super admin can delete users but not itself
        if (! $this->isSuperAdmin($user)) {
            return Response::deny('Hanya super_admin yang dapat menghapus pengguna.');
        }

        // Super admin cannot delete itself
        if ($user->id === $model->id) {
            return Response::deny('Anda tidak dapat menghapus akun diri sendiri.');
        }

        return Response::allow();
    }

    /**
     * Determine if the user can assign/manage roles and permissions
     */
    public function manageRoles(User $user): Response|bool
    {
        return $this->isSuperAdmin($user)
            ? Response::allow()
            : Response::deny('Hanya super_admin yang dapat mengatur roles dan permissions.');
    }
}
