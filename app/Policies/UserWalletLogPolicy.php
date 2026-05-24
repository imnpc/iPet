<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\UserWalletLog;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class UserWalletLogPolicy
{
    use HandlesAuthorization;

    public function view(AuthUser $authUser, UserWalletLog $userWalletLog): bool
    {
        return $authUser->can('view_UserWalletLog');
    }

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('viewAny_UserWalletLog');
    }
}
