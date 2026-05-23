<?php

namespace Database\Factories;

use App\Models\Pet;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'pet_id' => fake()->optional()->randomElement([null, Pet::factory()]),
            'content' => fake()->paragraph(2),
            'location' => fake()->optional()->city(),
            'visibility' => fake()->randomElement(['public', 'followers', 'private']),
            'like_count' => 0,
            'comment_count' => 0,
            'view_count' => 0,
            'share_count' => 0,
            'is_pinned' => false,
            'allow_comment' => true,
            'published_at' => now(),
        ];
    }
}
