<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PetSpecies;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PetSpeciesPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('viewAny_PetSpecies');
    }

    public function view(AuthUser $authUser, PetSpecies $petSpecies): bool
    {
        return $authUser->can('view_PetSpecies');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_PetSpecies');
    }

    public function update(AuthUser $authUser, PetSpecies $petSpecies): bool
    {
        return $authUser->can('update_PetSpecies');
    }

    public function delete(AuthUser $authUser, PetSpecies $petSpecies): bool
    {
        return $authUser->can('delete_PetSpecies');
    }

    public function restore(AuthUser $authUser, PetSpecies $petSpecies): bool
    {
        return $authUser->can('restore_PetSpecies');
    }

    public function forceDelete(AuthUser $authUser, PetSpecies $petSpecies): bool
    {
        return $authUser->can('forceDelete_PetSpecies');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('forceDeleteAny_PetSpecies');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restoreAny_PetSpecies');
    }

    public function replicate(AuthUser $authUser, PetSpecies $petSpecies): bool
    {
        return $authUser->can('replicate_PetSpecies');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_PetSpecies');
    }
}
