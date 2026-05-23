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
}
