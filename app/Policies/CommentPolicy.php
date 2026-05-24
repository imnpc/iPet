<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Comment;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CommentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('viewAny_Comment');
    }

    public function view(AuthUser $authUser, Comment $comment): bool
    {
        return $authUser->can('view_Comment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_Comment');
    }

    public function update(AuthUser $authUser, Comment $comment): bool
    {
        return $authUser->can('update_Comment');
    }

    public function delete(AuthUser $authUser, Comment $comment): bool
    {
        return $authUser->can('delete_Comment');
    }

    public function restore(AuthUser $authUser, Comment $comment): bool
    {
        return $authUser->can('restore_Comment');
    }

    public function forceDelete(AuthUser $authUser, Comment $comment): bool
    {
        return $authUser->can('forceDelete_Comment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('forceDeleteAny_Comment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restoreAny_Comment');
    }

    public function replicate(AuthUser $authUser, Comment $comment): bool
    {
        return $authUser->can('replicate_Comment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_Comment');
    }
}
