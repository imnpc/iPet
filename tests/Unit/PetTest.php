<?php

namespace Tests\Unit;

use App\Models\Pet;
use App\Models\User;
use Tests\TestCase;

class PetTest extends TestCase
{
    public function test_can_create_a_pet(): void
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->create([
            'user_id' => $user->id,
            'name' => '豆豆',
            'species' => '狗',
        ]);

        $this->assertEquals('豆豆', $pet->name);
        $this->assertEquals('狗', $pet->species);
        $this->assertEquals($user->id, $pet->user_id);
    }

    public function test_belongs_to_a_user(): void
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $pet->user);
        $this->assertEquals($user->id, $pet->user->id);
    }

    public function test_has_many_records(): void
    {
        $pet = Pet::factory()->create();
        $this->assertCount(0, $pet->records);
    }

    public function test_has_many_posts(): void
    {
        $pet = Pet::factory()->create();
        $this->assertCount(0, $pet->posts);
    }

    public function test_can_be_set_as_default(): void
    {
        $user = User::factory()->create();
        $pet1 = Pet::factory()->create(['user_id' => $user->id, 'is_default' => true]);
        $pet2 = Pet::factory()->create(['user_id' => $user->id, 'is_default' => false]);

        $this->assertTrue($pet1->is_default);
        $this->assertFalse($pet2->is_default);
    }
}
