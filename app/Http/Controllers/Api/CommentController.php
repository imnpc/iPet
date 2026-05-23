<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post as RoutePost;
use Spatie\RouteAttributes\Attributes\Prefix;

#[Group('评论', description: '动态评论管理', weight: 35)]
#[Prefix('comments')]
class CommentController extends Controller
{
    #[Get('', middleware: ['auth:sanctum'])]
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
        ]);

        $comments = Comment::with(['user', 'children.user'])
            ->where('post_id', $request->input('post_id'))
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 20));

        return $this->success(CommentResource::collection($comments));
    }

    #[RoutePost('', middleware: ['auth:sanctum'])]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'parent_id' => 'nullable|exists:comments,id',
            'content' => 'required|string|max:1000',
        ]);

        $post = Post::findOrFail($validated['post_id']);

        $comment = $request->user()->comments()->create([
            'post_id' => $validated['post_id'],
            'parent_id' => $validated['parent_id'] ?? null,
            'content' => $validated['content'],
        ]);

        $post->increment('comment_count');

        $comment->load('user');

        return $this->success(new CommentResource($comment), '评论成功', 201);
    }

    #[Delete('{id}', middleware: ['auth:sanctum'])]
    public function destroy(Request $request, int $id): JsonResponse
    {
        $comment = $request->user()->comments()->findOrFail($id);
        $comment->delete();

        $comment->post->decrement('comment_count');

        return $this->success(null, '评论删除成功');
    }
}
