<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Tests\TestCase;

class PostCategoryWebTest extends TestCase
{
    public function test_post_index_displays_pet_species_category_badge(): void
    {
        $author = User::factory()->createOne();
        $pet = Pet::factory()->for($author)->createOne([
            'name' => '奶糖',
            'species' => '猫',
        ]);

        Post::factory()->for($author)->for($pet)->createOne([
            'content' => '猫咪晒太阳',
            'published_at' => now(),
            'visibility' => 'public',
        ]);

        /** @var Authenticatable $authUser */
        $authUser = $author;

        $response = $this->actingAs($authUser)->get(route('posts.index'));

        $response->assertOk();
        $response->assertSee('猫咪晒太阳');
        $response->assertSee('猫', false);
    }

    public function test_post_index_can_filter_by_species(): void
    {
        $author = User::factory()->createOne();

        $catPet = Pet::factory()->for($author)->createOne([
            'species' => '猫',
        ]);

        $dogPet = Pet::factory()->for($author)->createOne([
            'species' => '狗',
        ]);

        Post::factory()->for($author)->for($catPet)->createOne([
            'content' => '只显示猫动态',
            'published_at' => now(),
            'visibility' => 'public',
        ]);

        Post::factory()->for($author)->for($dogPet)->createOne([
            'content' => '不应出现在猫筛选里',
            'published_at' => now(),
            'visibility' => 'public',
        ]);

        /** @var Authenticatable $authUser */
        $authUser = $author;

        $response = $this->actingAs($authUser)->get(route('posts.index', ['species' => '猫']));

        $response->assertOk();
        $response->assertSee('只显示猫动态');
        $response->assertDontSee('不应出现在猫筛选里');
    }

    public function test_post_show_displays_species_category_badge(): void
    {
        $author = User::factory()->createOne();
        $pet = Pet::factory()->for($author)->createOne([
            'species' => '狗',
        ]);

        $post = Post::factory()->for($author)->for($pet)->createOne([
            'content' => '狗狗散步',
            'published_at' => now(),
            'visibility' => 'public',
        ]);

        /** @var Authenticatable $authUser */
        $authUser = $author;

        $response = $this->actingAs($authUser)->get(route('posts.show', $post));

        $response->assertOk();
        $response->assertSee('狗狗散步');
        $response->assertSee('狗', false);
    }
}
