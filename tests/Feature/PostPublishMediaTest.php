<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\Post;
use App\Models\PostMedia;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostPublishMediaTest extends TestCase
{
    public function test_it_stores_post_media_on_default_filesystem_disk_with_sort_order(): void
    {
        $defaultDisk = config('filesystems.default');
        Storage::fake($defaultDisk);

        $user = User::factory()->createOne();
        $pet = Pet::factory()->for($user)->createOne();

        /** @var Authenticatable $authUser */
        $authUser = $user;

        $response = $this->actingAs($authUser)->post(route('posts.store'), [
            'content' => '测试发布内容',
            'pet_id' => $pet->id,
            'visibility' => 'public',
            'published_at' => now()->format('Y-m-d H:i:s'),
            'image_files' => [
                UploadedFile::fake()->image('first.jpg'),
                UploadedFile::fake()->image('second.jpg'),
            ],
            'video_files' => [
                UploadedFile::fake()->create('demo.mp4', 500, 'video/mp4'),
            ],
        ]);

        $response->assertRedirect(route('posts.index'));

        $post = Post::query()->firstOrFail();

        $media = PostMedia::query()
            ->where('post_id', $post->id)
            ->orderBy('sort_order')
            ->get();

        $this->assertCount(3, $media);
        $this->assertSame($defaultDisk, $media[0]->disk);
        $this->assertSame('image', $media[0]->type);
        $this->assertSame(0, $media[0]->sort_order);
        $this->assertSame('image', $media[1]->type);
        $this->assertSame(1, $media[1]->sort_order);
        $this->assertSame('video', $media[2]->type);
        $this->assertSame(2, $media[2]->sort_order);

        $this->assertTrue(Storage::disk($defaultDisk)->exists($media[0]->path));
        $this->assertTrue(Storage::disk($defaultDisk)->exists($media[1]->path));
        $this->assertTrue(Storage::disk($defaultDisk)->exists($media[2]->path));
    }

    public function test_it_rejects_post_when_pet_id_does_not_belong_to_current_user(): void
    {
        $currentUser = User::factory()->createOne();
        $otherUser = User::factory()->createOne();
        $otherPet = Pet::factory()->for($otherUser)->createOne();

        /** @var Authenticatable $authUser */
        $authUser = $currentUser;

        $response = $this->from(route('posts.create'))
            ->actingAs($authUser)
            ->post(route('posts.store'), [
                'content' => '尝试关联他人宠物',
                'pet_id' => $otherPet->id,
                'visibility' => 'public',
            ]);

        $response->assertRedirect(route('posts.create'));
        $response->assertSessionHasErrors('pet_id');
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_it_stores_selected_visibility_when_publishing_post(): void
    {
        $user = User::factory()->createOne();

        /** @var Authenticatable $authUser */
        $authUser = $user;

        $response = $this->actingAs($authUser)->post(route('posts.store'), [
            'content' => '仅粉丝可见动态',
            'visibility' => 'followers',
        ]);

        $response->assertRedirect(route('posts.index'));

        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'content' => '仅粉丝可见动态',
            'visibility' => 'followers',
        ]);
    }

    public function test_it_can_append_media_when_updating_post_and_keep_published_at_unchanged(): void
    {
        $defaultDisk = config('filesystems.default');
        Storage::fake($defaultDisk);

        $user = User::factory()->createOne();
        $post = Post::factory()->for($user)->createOne([
            'published_at' => now()->subDay(),
            'visibility' => 'public',
        ]);

        $originalPublishedAt = $post->published_at?->toDateTimeString();

        /** @var Authenticatable $authUser */
        $authUser = $user;

        $response = $this->actingAs($authUser)->put(route('posts.update', $post), [
            'content' => '更新后的内容',
            'visibility' => 'private',
            'published_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'image_files' => [
                UploadedFile::fake()->image('update-first.jpg'),
            ],
            'video_files' => [
                UploadedFile::fake()->create('update-demo.mp4', 500, 'video/mp4'),
            ],
        ]);

        $response->assertRedirect(route('posts.show', $post));

        $post->refresh();
        $this->assertSame($originalPublishedAt, $post->published_at?->toDateTimeString());
        $this->assertSame('private', $post->visibility);

        $media = PostMedia::query()->where('post_id', $post->id)->orderBy('sort_order')->get();
        $this->assertCount(2, $media);
        $this->assertSame('image', $media[0]->type);
        $this->assertSame('video', $media[1]->type);
        $this->assertTrue(Storage::disk($defaultDisk)->exists($media[0]->path));
        $this->assertTrue(Storage::disk($defaultDisk)->exists($media[1]->path));
    }

    public function test_it_can_delete_and_reorder_existing_media_when_updating_post(): void
    {
        $user = User::factory()->createOne();
        $post = Post::factory()->for($user)->createOne([
            'published_at' => now(),
            'visibility' => 'public',
        ]);

        $first = $post->media()->create([
            'type' => 'image',
            'disk' => 'public',
            'path' => 'posts/images/first.jpg',
            'sort_order' => 0,
        ]);

        $second = $post->media()->create([
            'type' => 'image',
            'disk' => 'public',
            'path' => 'posts/images/second.jpg',
            'sort_order' => 1,
        ]);

        $third = $post->media()->create([
            'type' => 'video',
            'disk' => 'public',
            'path' => 'posts/videos/third.mp4',
            'sort_order' => 2,
        ]);

        /** @var Authenticatable $authUser */
        $authUser = $user;

        $response = $this->actingAs($authUser)->put(route('posts.update', $post), [
            'content' => '调整媒体顺序并删除',
            'visibility' => 'public',
            'keep_media_ids' => [$second->id, $first->id],
            'media_order' => [$second->id, $first->id],
        ]);

        $response->assertRedirect(route('posts.show', $post));

        $sortedMedia = $post->media()->orderBy('sort_order')->get();
        $this->assertCount(2, $sortedMedia);
        $this->assertSame($second->id, $sortedMedia[0]->id);
        $this->assertSame($first->id, $sortedMedia[1]->id);

        $this->assertSoftDeleted('post_media', ['id' => $third->id]);
    }
}
