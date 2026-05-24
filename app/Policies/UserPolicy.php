<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class UserPolicy
{
    use HandlesAuthorization;

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('view_User');
    }

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('viewAny_User');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_User');
    }

    public function update(AuthUser $authUser): bool
    {
        return $authUser->can('update_User');
    }

    public function delete(AuthUser $authUser): bool
    {
        return $authUser->can('delete_User');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('deleteAny_User');
    }
}
