<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use TomatoPHP\FilamentWallet\Models\Transaction;

class TransactionPolicy
{
    use HandlesAuthorization;

    public function view(AuthUser $authUser, Transaction $transaction): bool
    {
        return $authUser->can('view_Transaction');
    }

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('viewAny_Transaction');
    }
}
