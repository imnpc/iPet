<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PetRecordType;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PetRecordTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('viewAny_PetRecordType');
    }

    public function view(AuthUser $authUser, PetRecordType $petRecordType): bool
    {
        return $authUser->can('view_PetRecordType');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_PetRecordType');
    }

    public function update(AuthUser $authUser, PetRecordType $petRecordType): bool
    {
        return $authUser->can('update_PetRecordType');
    }

    public function delete(AuthUser $authUser, PetRecordType $petRecordType): bool
    {
        return $authUser->can('delete_PetRecordType');
    }

    public function restore(AuthUser $authUser, PetRecordType $petRecordType): bool
    {
        return $authUser->can('restore_PetRecordType');
    }

    public function forceDelete(AuthUser $authUser, PetRecordType $petRecordType): bool
    {
        return $authUser->can('forceDelete_PetRecordType');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('forceDeleteAny_PetRecordType');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restoreAny_PetRecordType');
    }

    public function replicate(AuthUser $authUser, PetRecordType $petRecordType): bool
    {
        return $authUser->can('replicate_PetRecordType');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_PetRecordType');
    }
}
