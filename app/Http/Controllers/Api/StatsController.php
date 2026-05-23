<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Pet;
use App\Models\PetRecord;
use App\Models\Post;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;

#[Group('统计', description: '数据统计看板', weight: 55)]
#[Prefix('stats')]
#[Middleware(['auth:sanctum'])]
class StatsController extends Controller
{
    #[Get('dashboard')]
    public function dashboard()
    {
        $stats = [
            'users' => User::count(),
            'pets' => Pet::count(),
            'posts' => Post::where('visibility', 'public')->whereNotNull('published_at')->count(),
            'records' => PetRecord::count(),
            'likes' => Like::count(),
            'comments' => Comment::count(),
        ];

        $recentPosts = Post::with(['user', 'pet'])
            ->where('visibility', 'public')
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get(['id', 'content', 'user_id', 'pet_id', 'published_at']);

        return $this->success([
            'stats' => $stats,
            'recent_posts' => $recentPosts,
        ]);
    }

    #[Get('user')]
    public function user(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return $this->error('未登录', 401);
        }

        $stats = [
            'pets_count' => $user->pets()->count(),
            'posts_count' => $user->posts()->where('visibility', 'public')->whereNotNull('published_at')->count(),
            'records_count' => PetRecord::whereIn('pet_id', $user->pets()->pluck('id'))->count(),
            'followers_count' => $user->followers()->count(),
            'following_count' => $user->following()->count(),
            'likes_received' => Like::whereIn('likeable_id', $user->posts()->pluck('id'))
                ->where('likeable_type', Post::class)
                ->count(),
        ];

        return $this->success($stats);
    }
}
