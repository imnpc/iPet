<?php

namespace Database\Factories;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetFactory extends Factory
{
    protected $model = Pet::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->firstName(),
            'species' => fake()->randomElement(['狗', '猫', '兔子', '仓鼠', '其他']),
            'breed' => fake()->word(),
            'gender' => fake()->randomElement(['male', 'female', 'unknown']),
            'birthday' => fake()->optional()->date(),
            'adoption_date' => fake()->optional()->date(),
            'avatar' => null,
            'metadata' => null,
            'is_default' => false,
            'status' => 'active',
            'sort_order' => 0,
        ];
    }
}
