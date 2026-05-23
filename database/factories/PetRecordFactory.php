<?php

namespace Database\Factories;

use App\Models\Pet;
use App\Models\PetRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetRecordFactory extends Factory
{
    protected $model = PetRecord::class;

    public function definition(): array
    {
        return [
            'pet_id' => Pet::factory(),
            'type' => fake()->randomElement(['vaccine', 'checkup', 'illness', 'medication', 'surgery', 'grooming', 'other']),
            'title' => fake()->sentence(3),
            'visit_date' => fake()->date(),
            'next_visit_date' => fake()->optional()->date(),
            'hospital_name' => fake()->optional()->company(),
            'vet_name' => fake()->optional()->name(),
            'hospital_phone' => fake()->optional()->phoneNumber(),
            'weight' => fake()->optional()->randomFloat(2, 0.5, 50),
            'temperature' => fake()->optional()->randomFloat(1, 36, 40),
            'symptoms' => fake()->optional()->paragraph(),
            'diagnosis' => fake()->optional()->paragraph(),
            'treatment' => fake()->optional()->paragraph(),
            'prescription' => fake()->optional()->paragraph(),
            'notes' => fake()->optional()->paragraph(),
            'cost' => fake()->optional()->randomFloat(2, 50, 5000),
        ];
    }
}
