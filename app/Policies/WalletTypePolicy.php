<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\WalletType;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class WalletTypePolicy
{
    use HandlesAuthorization;

    public function view(AuthUser $authUser, WalletType $walletType): bool
    {
        return $authUser->can('view_WalletType');
    }

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('viewAny_WalletType');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_WalletType');
    }

    public function update(AuthUser $authUser, WalletType $walletType): bool
    {
        return $authUser->can('update_WalletType');
    }

    public function delete(AuthUser $authUser, WalletType $walletType): bool
    {
        return $authUser->can('delete_WalletType');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('deleteAny_WalletType');
    }
}
