<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Pet;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PetPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('viewAny_Pet');
    }

    public function view(AuthUser $authUser, Pet $pet): bool
    {
        return $authUser->can('view_Pet');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_Pet');
    }

    public function update(AuthUser $authUser, Pet $pet): bool
    {
        return $authUser->can('update_Pet');
    }

    public function delete(AuthUser $authUser, Pet $pet): bool
    {
        return $authUser->can('delete_Pet');
    }

    public function restore(AuthUser $authUser, Pet $pet): bool
    {
        return $authUser->can('restore_Pet');
    }

    public function forceDelete(AuthUser $authUser, Pet $pet): bool
    {
        return $authUser->can('forceDelete_Pet');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('forceDeleteAny_Pet');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restoreAny_Pet');
    }

    public function replicate(AuthUser $authUser, Pet $pet): bool
    {
        return $authUser->can('replicate_Pet');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_Pet');
    }
}
