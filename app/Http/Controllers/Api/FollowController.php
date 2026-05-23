<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;

#[Group('关注', description: '用户关注管理', weight: 36)]
#[Prefix('follows')]
class FollowController extends Controller
{
    #[Get('followers', middleware: ['auth:sanctum'])]
    public function followers(Request $request): JsonResponse
    {
        $followers = $request->user()->followers()
            ->with('follower')
            ->paginate($request->input('per_page', 20));

        return $this->success($followers);
    }

    #[Get('following', middleware: ['auth:sanctum'])]
    public function following(Request $request): JsonResponse
    {
        $following = $request->user()->following()
            ->with('following')
            ->paginate($request->input('per_page', 20));

        return $this->success($following);
    }

    #[Post('', middleware: ['auth:sanctum'])]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|not_in:'.$request->user()->id,
        ]);

        $follow = $request->user()->follows()->firstOrCreate([
            'following_id' => $validated['user_id'],
        ], [
            'created_at' => now(),
        ]);

        return $this->success($follow, '关注成功', 201);
    }

    #[Delete('{user_id}', middleware: ['auth:sanctum'])]
    public function destroy(Request $request, int $user_id): JsonResponse
    {
        $request->user()->follows()
            ->where('following_id', $user_id)
            ->delete();

        return $this->success(null, '取消关注成功');
    }
}
