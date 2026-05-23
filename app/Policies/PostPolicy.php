<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Post;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PostPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('viewAny_Post');
    }

    public function view(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('view_Post');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_Post');
    }

    public function update(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('update_Post');
    }

    public function delete(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('delete_Post');
    }

    public function restore(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('restore_Post');
    }

    public function forceDelete(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('forceDelete_Post');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('forceDeleteAny_Post');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restoreAny_Post');
    }

    public function replicate(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('replicate_Post');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_Post');
    }
}
