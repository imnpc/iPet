<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Tests\TestCase;

class PostCommentWebTest extends TestCase
{
    public function test_post_view_count_increases_when_opening_post_show_page(): void
    {
        $viewer = User::factory()->createOne();
        $author = User::factory()->createOne();
        $post = Post::factory()->for($author)->createOne([
            'published_at' => now(),
            'visibility' => 'public',
            'pet_id' => null,
            'view_count' => 0,
        ]);

        /** @var Authenticatable $authUser */
        $authUser = $viewer;

        $this->actingAs($authUser)
            ->get(route('posts.show', $post))
            ->assertOk();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'view_count' => 1,
        ]);
    }

    public function test_post_view_count_is_deduplicated_within_minutes_for_same_user(): void
    {
        config()->set('app.post_view_deduplicate_minutes', 10);

        $viewer = User::factory()->createOne();
        $author = User::factory()->createOne();
        $post = Post::factory()->for($author)->createOne([
            'published_at' => now(),
            'visibility' => 'public',
            'pet_id' => null,
            'view_count' => 0,
        ]);

        /** @var Authenticatable $authUser */
        $authUser = $viewer;

        $this->actingAs($authUser)
            ->get(route('posts.show', $post))
            ->assertOk();

        $this->actingAs($authUser)
            ->get(route('posts.show', $post))
            ->assertOk();

        $post->refresh();
        $this->assertSame(1, $post->view_count);

        $this->travel(11)->minutes();

        $this->actingAs($authUser)
            ->get(route('posts.show', $post))
            ->assertOk();

        $post->refresh();
        $this->assertSame(2, $post->view_count);
    }

    public function test_authenticated_user_can_publish_comment_from_post_show_page(): void
    {
        $user = User::factory()->createOne();
        $author = User::factory()->createOne();
        $post = Post::factory()->for($author)->createOne([
            'allow_comment' => true,
            'published_at' => now(),
            'visibility' => 'public',
        ]);

        /** @var Authenticatable $authUser */
        $authUser = $user;

        $response = $this->actingAs($authUser)->post(route('posts.comments.store', $post), [
            'content' => '网页端评论提交测试',
        ]);

        $response->assertRedirect(route('posts.show', $post));

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'content' => '网页端评论提交测试',
        ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'comment_count' => 1,
        ]);
    }

    public function test_authenticated_user_can_see_reply_target_profile_link_in_post_show_page(): void
    {
        $viewer = User::factory()->createOne();
        $author = User::factory()->createOne(['name' => '被回复用户']);
        $post = Post::factory()->for($author)->createOne([
            'allow_comment' => true,
            'published_at' => now(),
            'visibility' => 'public',
            'comment_count' => 1,
        ]);

        $parentComment = Comment::query()->create([
            'post_id' => $post->id,
            'user_id' => $author->id,
            'content' => '父评论',
        ]);

        Comment::query()->create([
            'post_id' => $post->id,
            'user_id' => $viewer->id,
            'parent_id' => $parentComment->id,
            'content' => '子评论',
        ]);

        /** @var Authenticatable $authUser */
        $authUser = $viewer;

        $response = $this->actingAs($authUser)->get(route('posts.show', $post));

        $response->assertOk();
        $response->assertSee(route('users.show', $author), false);
        $response->assertSee('@'.$author->name);
    }

    public function test_authenticated_user_can_reply_to_existing_comment_from_post_show_page(): void
    {
        $user = User::factory()->createOne();
        $author = User::factory()->createOne();
        $post = Post::factory()->for($author)->createOne([
            'allow_comment' => true,
            'published_at' => now(),
            'visibility' => 'public',
            'comment_count' => 1,
        ]);

        $parentComment = Comment::query()->create([
            'post_id' => $post->id,
            'user_id' => $author->id,
            'content' => '父评论',
        ]);

        /** @var Authenticatable $authUser */
        $authUser = $user;

        $response = $this->actingAs($authUser)->post(route('posts.comments.store', $post), [
            'parent_id' => $parentComment->id,
            'content' => '网页端回复评论测试',
        ]);

        $response->assertRedirect(route('posts.show', $post));

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'parent_id' => $parentComment->id,
            'content' => '网页端回复评论测试',
        ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'comment_count' => 2,
        ]);
    }

    public function test_post_show_only_renders_top_level_comments_once_and_nests_children(): void
    {
        $viewer = User::factory()->createOne();
        $author = User::factory()->createOne();
        $replyUser = User::factory()->createOne();

        $post = Post::factory()->for($author)->createOne([
            'allow_comment' => true,
            'published_at' => now(),
            'visibility' => 'public',
            'comment_count' => 2,
        ]);

        $parentComment = Comment::query()->create([
            'post_id' => $post->id,
            'user_id' => $author->id,
            'content' => '父评论内容',
        ]);

        $childComment = Comment::query()->create([
            'post_id' => $post->id,
            'user_id' => $replyUser->id,
            'parent_id' => $parentComment->id,
            'content' => '子评论内容',
        ]);

        $authViewer = User::query()->findOrFail($viewer->id);

        $response = $this->actingAs($authViewer)->get(route('posts.show', $post));

        $response->assertOk();
        $response->assertSee('父评论内容', false);
        $response->assertSee('子评论内容', false);

        $markup = $response->getContent();
        $this->assertIsString($markup);
        $this->assertSame(1, substr_count($markup, '父评论内容'));
        $this->assertSame(1, substr_count($markup, '子评论内容'));
        $this->assertStringContainsString('name="parent_id" value="'.$parentComment->id.'"', $markup);
        $this->assertStringNotContainsString('name="parent_id" value="'.$childComment->id.'"', $markup);
    }

    public function test_authenticated_user_can_like_top_level_comment_from_post_show_page(): void
    {
        $viewer = User::factory()->createOne();
        $author = User::factory()->createOne();

        $post = Post::factory()->for($author)->createOne([
            'allow_comment' => true,
            'published_at' => now(),
            'visibility' => 'public',
            'comment_count' => 1,
        ]);

        $comment = Comment::query()->create([
            'post_id' => $post->id,
            'user_id' => $author->id,
            'content' => '待点赞评论',
            'like_count' => 0,
        ]);

        /** @var Authenticatable $authViewer */
        $authViewer = $viewer;

        $response = $this->actingAs($authViewer)->post(route('posts.comments.like', ['post' => $post, 'comment' => $comment]), [
            'comment_sort' => 'hot',
        ]);

        $response->assertRedirect(route('posts.show', ['post' => $post, 'comment_sort' => 'hot']));

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'like_count' => 1,
        ]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $viewer->id,
            'likeable_type' => Comment::class,
            'likeable_id' => $comment->id,
        ]);
    }

    public function test_authenticated_user_can_toggle_comment_like(): void
    {
        $viewer = User::factory()->createOne();
        $author = User::factory()->createOne();

        $post = Post::factory()->for($author)->createOne([
            'allow_comment' => true,
            'published_at' => now(),
            'visibility' => 'public',
            'comment_count' => 1,
        ]);

        $comment = Comment::query()->create([
            'post_id' => $post->id,
            'user_id' => $author->id,
            'content' => '不可重复点赞评论',
            'like_count' => 0,
        ]);

        /** @var Authenticatable $authViewer */
        $authViewer = $viewer;

        $this->actingAs($authViewer)->post(route('posts.comments.like', ['post' => $post, 'comment' => $comment]), [
            'comment_sort' => 'time',
        ])->assertRedirect(route('posts.show', ['post' => $post, 'comment_sort' => 'time']));

        $this->actingAs($authViewer)->post(route('posts.comments.like', ['post' => $post, 'comment' => $comment]), [
            'comment_sort' => 'time',
        ])->assertRedirect(route('posts.show', ['post' => $post, 'comment_sort' => 'time']));

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'like_count' => 0,
        ]);

        $this->assertDatabaseCount('likes', 0);
    }

    public function test_post_show_can_sort_top_level_comments_by_hot(): void
    {
        $viewer = User::factory()->createOne();
        $author = User::factory()->createOne();

        $post = Post::factory()->for($author)->createOne([
            'allow_comment' => true,
            'published_at' => now(),
            'visibility' => 'public',
            'comment_count' => 2,
        ]);

        Comment::query()->create([
            'post_id' => $post->id,
            'user_id' => $author->id,
            'content' => '低热度评论',
            'like_count' => 1,
        ]);

        Comment::query()->create([
            'post_id' => $post->id,
            'user_id' => $author->id,
            'content' => '高热度评论',
            'like_count' => 10,
        ]);

        /** @var Authenticatable $authViewer */
        $authViewer = $viewer;

        $response = $this->actingAs($authViewer)->get(route('posts.show', ['post' => $post, 'comment_sort' => 'hot']));
        $response->assertOk();

        $markup = $response->getContent();
        $this->assertIsString($markup);

        $highPosition = strpos($markup, '高热度评论');
        $lowPosition = strpos($markup, '低热度评论');

        $this->assertNotFalse($highPosition);
        $this->assertNotFalse($lowPosition);
        $this->assertTrue($highPosition < $lowPosition);
    }

    public function test_post_show_displays_liked_state_for_comment_and_child_comment(): void
    {
        $viewer = User::factory()->createOne();
        $author = User::factory()->createOne();

        $post = Post::factory()->for($author)->createOne([
            'allow_comment' => true,
            'published_at' => now(),
            'visibility' => 'public',
            'comment_count' => 2,
        ]);

        $parentComment = Comment::query()->create([
            'post_id' => $post->id,
            'user_id' => $author->id,
            'content' => '已点赞父评论',
            'like_count' => 1,
        ]);

        $childComment = Comment::query()->create([
            'post_id' => $post->id,
            'user_id' => $author->id,
            'parent_id' => $parentComment->id,
            'content' => '已点赞子评论',
            'like_count' => 1,
        ]);

        $parentComment->likes()->create([
            'user_id' => $viewer->id,
            'created_at' => now(),
        ]);

        $childComment->likes()->create([
            'user_id' => $viewer->id,
            'created_at' => now(),
        ]);

        /** @var Authenticatable $authViewer */
        $authViewer = $viewer;

        $response = $this->actingAs($authViewer)->get(route('posts.show', $post));

        $response->assertOk();
        $response->assertSee('已点赞 1', false);

        $markup = $response->getContent();
        $this->assertIsString($markup);
        $this->assertGreaterThanOrEqual(2, substr_count($markup, '已点赞 1'));
    }

    public function test_authenticated_user_can_like_post_from_post_stats_component(): void
    {
        $user = User::factory()->createOne();
        $author = User::factory()->createOne();

        $post = Post::factory()->for($author)->createOne([
            'published_at' => now(),
            'visibility' => 'public',
            'like_count' => 0,
        ]);

        /** @var Authenticatable $authUser */
        $authUser = $user;

        $response = $this->actingAs($authUser)->post(route('posts.like', $post), [
            'return_to' => route('posts.show', $post),
        ]);

        $response->assertRedirect(route('posts.show', $post));

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'like_count' => 1,
        ]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'likeable_type' => Post::class,
            'likeable_id' => $post->id,
        ]);
    }

    public function test_authenticated_user_can_toggle_post_like(): void
    {
        $user = User::factory()->createOne();
        $author = User::factory()->createOne();

        $post = Post::factory()->for($author)->createOne([
            'published_at' => now(),
            'visibility' => 'public',
            'like_count' => 0,
        ]);

        /** @var Authenticatable $authUser */
        $authUser = $user;

        $this->actingAs($authUser)->post(route('posts.like', $post), [
            'return_to' => route('posts.show', $post),
        ])->assertRedirect(route('posts.show', $post));

        $this->actingAs($authUser)->post(route('posts.like', $post), [
            'return_to' => route('posts.show', $post),
        ])->assertRedirect(route('posts.show', $post));

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'like_count' => 0,
        ]);

        $this->assertDatabaseCount('likes', 0);
    }
}
