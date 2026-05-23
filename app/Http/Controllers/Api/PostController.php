<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post as RoutePost;
use Spatie\RouteAttributes\Attributes\Prefix;

#[Group('动态', description: '动态发布与管理', weight: 30)]
#[Prefix('posts')]
class PostController extends Controller
{
    #[Get('')]
    public function index(Request $request): JsonResponse
    {
        $query = Post::with(['user', 'pet', 'media'])
            ->published()
            ->visibleTo($request->user());

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('pet_id')) {
            $query->where('pet_id', $request->input('pet_id'));
        }

        if ($request->filled('tag')) {
            $query->withAnyTags([$request->input('tag')]);
        }

        $posts = $query->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->paginate($request->input('per_page', 20));

        return $this->success(PostResource::collection($posts));
    }

    #[RoutePost('', middleware: ['auth:sanctum'])]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'pet_id' => 'nullable|exists:pets,id',
            'location' => 'nullable|string|max:200',
            'visibility' => 'in:public,followers,private',
            'media' => 'nullable|array',
            'media.*.type' => 'required|in:image,video',
            'media.*.disk' => 'nullable|string|max:30',
            'media.*.path' => 'required|string',
            'media.*.thumbnail_path' => 'nullable|string',
            'media.*.mime_type' => 'nullable|string|max:100',
            'media.*.size' => 'nullable|integer',
            'media.*.width' => 'nullable|integer',
            'media.*.height' => 'nullable|integer',
            'media.*.duration' => 'nullable|integer',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        $user = $request->user();

        $post = DB::transaction(function () use ($user, $validated) {
            $post = $user->posts()->create([
                'content' => $validated['content'],
                'pet_id' => $validated['pet_id'] ?? null,
                'location' => $validated['location'] ?? null,
                'visibility' => $validated['visibility'] ?? 'public',
                'published_at' => now(),
            ]);

            if (! empty($validated['media'])) {
                foreach ($validated['media'] as $index => $mediaItem) {
                    $post->media()->create([
                        'type' => $mediaItem['type'],
                        'disk' => $mediaItem['disk'] ?? config('filesystems.default'),
                        'path' => $mediaItem['path'],
                        'thumbnail_path' => $mediaItem['thumbnail_path'] ?? null,
                        'mime_type' => $mediaItem['mime_type'] ?? null,
                        'size' => $mediaItem['size'] ?? null,
                        'width' => $mediaItem['width'] ?? null,
                        'height' => $mediaItem['height'] ?? null,
                        'duration' => $mediaItem['duration'] ?? null,
                        'sort_order' => $index,
                    ]);
                }
            }

            if (! empty($validated['tags'])) {
                $post->attachTags($validated['tags']);
            }

            return $post;
        });

        $post->load(['user', 'pet', 'media']);

        return $this->success(new PostResource($post), '动态发布成功', 201);
    }

    #[Get('{id}')]
    public function show(Request $request, int $id): JsonResponse
    {
        $post = Post::with(['user', 'pet', 'media'])
            ->published()
            ->visibleTo($request->user())
            ->findOrFail($id);

        return $this->success(new PostResource($post));
    }

    #[Delete('{id}', middleware: ['auth:sanctum'])]
    public function destroy(Request $request, int $id): JsonResponse
    {
        $post = $request->user()->posts()->findOrFail($id);
        $post->delete();

        return $this->success(null, '动态删除成功');
    }

    #[RoutePost('{id}/like', middleware: ['auth:sanctum'])]
    public function like(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $post = Post::published()
            ->visibleTo($user)
            ->findOrFail($id);

        $like = $user->likes()
            ->where('likeable_type', Post::class)
            ->where('likeable_id', $post->id)
            ->first();

        if ($like) {
            $like->delete();
            $post->decrement('like_count');

            return $this->success(['liked' => false], '取消点赞成功');
        }

        $user->likes()->create([
            'likeable_type' => Post::class,
            'likeable_id' => $post->id,
            'created_at' => now(),
        ]);
        $post->increment('like_count');

        return $this->success(['liked' => true], '点赞成功');
    }
}
