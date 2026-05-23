<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class PostTest extends TestCase
{
    public function test_can_create_a_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'content' => '今天带豆豆去公园玩了！',
            'visibility' => 'public',
        ]);

        $this->assertEquals('今天带豆豆去公园玩了！', $post->content);
        $this->assertEquals('public', $post->visibility);
        $this->assertEquals($user->id, $post->user_id);
    }

    public function test_belongs_to_a_user(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $post->user);
        $this->assertEquals($user->id, $post->user->id);
    }

    public function test_can_have_media(): void
    {
        $post = Post::factory()->create();
        $this->assertCount(0, $post->media);
    }

    public function test_supports_soft_delete(): void
    {
        $post = Post::factory()->create();
        $post->delete();
        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }
}
