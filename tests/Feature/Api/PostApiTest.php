<?php

namespace Tests\Feature\Api;

use App\Models\Follow;
use App\Models\Post;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    public function test_can_list_public_posts(): void
    {
        Post::factory()->count(5)->create(['visibility' => 'public', 'published_at' => now()]);

        $response = $this->getJson('/api/v1/posts');
        $response->assertOk()->assertJsonPath('status', 'success');
    }

    public function test_can_create_a_post(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);
        $response = $this->postJson('/api/v1/posts', [
            'content' => '今天带宠物去公园！',
            'visibility' => 'public',
        ]);

        $response->assertCreated()->assertJsonPath('data.content', '今天带宠物去公园！');

        $this->assertDatabaseHas('posts', ['content' => '今天带宠物去公园！', 'user_id' => $user->id]);
    }

    public function test_can_delete_own_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);
        $response = $this->deleteJson("/api/v1/posts/{$post->id}");
        $response->assertOk();
        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    public function test_guest_cannot_view_private_post_by_id(): void
    {
        $post = Post::factory()->create([
            'visibility' => 'private',
            'published_at' => now(),
        ]);

        $this->getJson("/api/v1/posts/{$post->id}")->assertNotFound();
    }

    public function test_owner_can_view_private_post_by_id(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'visibility' => 'private',
            'published_at' => now(),
        ]);

        Sanctum::actingAs($user);

        $this->getJson("/api/v1/posts/{$post->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $post->id);
    }

    public function test_follower_can_view_followers_only_post_by_id(): void
    {
        $author = User::factory()->create();
        $follower = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $author->id,
            'visibility' => 'followers',
            'published_at' => now(),
        ]);

        Follow::query()->create([
            'follower_id' => $follower->id,
            'following_id' => $author->id,
            'created_at' => now(),
        ]);

        Sanctum::actingAs($follower);

        $this->getJson("/api/v1/posts/{$post->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $post->id);
    }

    public function test_can_like_a_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'visibility' => 'public',
            'published_at' => now(),
        ]);

        Sanctum::actingAs($user);
        $response = $this->postJson("/api/v1/posts/{$post->id}/like");
        $response->assertOk()->assertJsonPath('data.liked', true);
    }

    public function test_can_unlike_a_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'visibility' => 'public',
            'published_at' => now(),
        ]);

        Sanctum::actingAs($user);
        $this->postJson("/api/v1/posts/{$post->id}/like");
        Sanctum::actingAs($user);
        $response = $this->postJson("/api/v1/posts/{$post->id}/like");
        $response->assertOk()->assertJsonPath('data.liked', false);
    }
}
