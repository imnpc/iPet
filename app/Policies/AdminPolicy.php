<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class AdminPolicy
{
    use HandlesAuthorization;

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('view_Admin');
    }

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('viewAny_Admin');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_Admin');
    }

    public function update(AuthUser $authUser): bool
    {
        return $authUser->can('update_Admin');
    }

    public function delete(AuthUser $authUser): bool
    {
        return $authUser->can('delete_Admin');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('deleteAny_Admin');
    }
}
