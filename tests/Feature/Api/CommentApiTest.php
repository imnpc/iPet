<?php

namespace Tests\Feature\Api;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommentApiTest extends TestCase
{
    public function test_can_list_comments_without_exposing_raw_user_fields(): void
    {
        $viewer = User::factory()->create();
        $author = User::factory()->create([
            'email' => 'comment-author@example.com',
            'mobile' => '18800000000',
        ]);
        $post = Post::factory()->create([
            'visibility' => 'public',
            'published_at' => now(),
        ]);

        Comment::query()->create([
            'post_id' => $post->id,
            'user_id' => $author->id,
            'content' => '这是一条安全的 API 评论',
        ]);

        Sanctum::actingAs($viewer);

        $response = $this->getJson("/api/v1/comments?post_id={$post->id}");

        $response->assertOk()
            ->assertJsonFragment(['content' => '这是一条安全的 API 评论'])
            ->assertJsonFragment(['name' => $author->name])
            ->assertJsonMissing(['email' => 'comment-author@example.com'])
            ->assertJsonMissing(['mobile' => '18800000000'])
            ->assertJsonMissingPath('data.data.0.deleted_at');
    }
}
