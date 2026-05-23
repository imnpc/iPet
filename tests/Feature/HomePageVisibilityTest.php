<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_only_sees_public_posts_on_homepage(): void
    {
        $publicPost = Post::factory()->create([
            'content' => '游客可见动态',
            'visibility' => 'public',
            'published_at' => now(),
        ]);
        $followersPost = Post::factory()->create([
            'content' => '游客不可见仅关注者动态',
            'visibility' => 'followers',
            'published_at' => now(),
        ]);
        $privatePost = Post::factory()->create([
            'content' => '游客不可见私密动态',
            'visibility' => 'private',
            'published_at' => now(),
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee($publicPost->content);
        $response->assertDontSee($followersPost->content);
        $response->assertDontSee($privatePost->content);
    }

    public function test_authenticated_user_can_see_own_non_public_posts_on_homepage(): void
    {
        $currentUser = User::factory()->create();
        $otherUser = User::factory()->create();

        $ownFollowersPost = Post::factory()->for($currentUser)->create([
            'content' => '我的仅关注者动态',
            'visibility' => 'followers',
            'published_at' => now(),
        ]);
        $ownPrivatePost = Post::factory()->for($currentUser)->create([
            'content' => '我的私密动态',
            'visibility' => 'private',
            'published_at' => now(),
        ]);
        $otherPrivatePost = Post::factory()->for($otherUser)->create([
            'content' => '他人的私密动态',
            'visibility' => 'private',
            'published_at' => now(),
        ]);
        $publicPost = Post::factory()->for($otherUser)->create([
            'content' => '公开动态',
            'visibility' => 'public',
            'published_at' => now(),
        ]);

        $response = $this->actingAs($currentUser)->get(route('home'));

        $response->assertOk();
        $response->assertSee($ownFollowersPost->content);
        $response->assertSee($ownPrivatePost->content);
        $response->assertSee($publicPost->content);
        $response->assertDontSee($otherPrivatePost->content);
    }
}
