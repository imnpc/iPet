<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PetRecord;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PetRecordPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('viewAny_PetRecord');
    }

    public function view(AuthUser $authUser, PetRecord $petRecord): bool
    {
        return $authUser->can('view_PetRecord');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_PetRecord');
    }

    public function update(AuthUser $authUser, PetRecord $petRecord): bool
    {
        return $authUser->can('update_PetRecord');
    }

    public function delete(AuthUser $authUser, PetRecord $petRecord): bool
    {
        return $authUser->can('delete_PetRecord');
    }

    public function restore(AuthUser $authUser, PetRecord $petRecord): bool
    {
        return $authUser->can('restore_PetRecord');
    }

    public function forceDelete(AuthUser $authUser, PetRecord $petRecord): bool
    {
        return $authUser->can('forceDelete_PetRecord');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('forceDeleteAny_PetRecord');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restoreAny_PetRecord');
    }

    public function replicate(AuthUser $authUser, PetRecord $petRecord): bool
    {
        return $authUser->can('replicate_PetRecord');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_PetRecord');
    }
}
