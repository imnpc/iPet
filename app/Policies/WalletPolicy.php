<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use TomatoPHP\FilamentWallet\Models\Wallet;

class WalletPolicy
{
    use HandlesAuthorization;

    public function view(AuthUser $authUser, Wallet $wallet): bool
    {
        return $authUser->can('view_Wallet');
    }

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('viewAny_Wallet');
    }
}
