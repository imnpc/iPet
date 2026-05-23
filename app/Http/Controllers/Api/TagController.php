<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\Tags\Tag;

#[Group('标签', description: '标签管理', weight: 45)]
#[Prefix('tags')]
class TagController extends Controller
{
    #[Get('')]
    public function index(Request $request): JsonResponse
    {
        $query = Tag::query();

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $tags = $query->orderBy('name')->get();

        return $this->success($tags->map(fn ($tag) => [
            'id' => $tag->id,
            'name' => $tag->name,
            'slug' => $tag->slug,
            'type' => $tag->type,
        ]));
    }

    #[Get('hot')]
    public function hot(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 20);
        $type = $request->input('type', 'post');

        $tags = Tag::withCount([
            'posts' => fn ($q) => $q->where('visibility', 'public')->whereNotNull('published_at'),
        ])
            ->orderBy('posts_count', 'desc')
            ->limit($limit)
            ->get();

        return $this->success($tags->map(fn ($tag) => [
            'id' => $tag->id,
            'name' => $tag->name,
            'slug' => $tag->slug,
            'count' => $tag->posts_count ?? 0,
        ]));
    }
}
