<?php

namespace Tests\Feature\Api;

use App\Models\Pet;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PetApiTest extends TestCase
{
    public function test_can_list_pets(): void
    {
        $user = User::factory()->create();
        Pet::factory()->count(3)->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v1/pets');

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_a_pet(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);
        $response = $this->postJson('/api/v1/pets', [
            'name' => '豆豆',
            'species' => '狗',
            'breed' => '金毛',
            'gender' => 'male',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', '豆豆');

        $this->assertDatabaseHas('pets', ['name' => '豆豆', 'user_id' => $user->id]);
    }

    public function test_can_show_a_pet(): void
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $user->id, 'name' => '咪咪']);

        Sanctum::actingAs($user);
        $response = $this->getJson("/api/v1/pets/{$pet->id}");
        $response->assertOk()->assertJsonPath('data.name', '咪咪');
    }

    public function test_can_update_a_pet(): void
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $user->id, 'name' => '旧名字']);

        Sanctum::actingAs($user);
        $response = $this->putJson("/api/v1/pets/{$pet->id}", ['name' => '新名字']);
        $response->assertOk()->assertJsonPath('data.name', '新名字');
        $pet->refresh();
        $this->assertEquals('新名字', $pet->name);
    }

    public function test_can_delete_a_pet(): void
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);
        $response = $this->deleteJson("/api/v1/pets/{$pet->id}");
        $response->assertOk();
        $this->assertSoftDeleted('pets', ['id' => $pet->id]);
    }

    public function test_cannot_access_another_users_pet(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $user2->id]);

        Sanctum::actingAs($user1);
        $response = $this->getJson("/api/v1/pets/{$pet->id}");
        $response->assertNotFound();
    }

    public function test_can_set_default_pet_atomically(): void
    {
        $user = User::factory()->create();
        $oldDefaultPet = Pet::factory()->create([
            'user_id' => $user->id,
            'is_default' => true,
        ]);
        $newDefaultPet = Pet::factory()->create([
            'user_id' => $user->id,
            'is_default' => false,
        ]);

        Sanctum::actingAs($user);

        $this->putJson("/api/v1/pets/{$newDefaultPet->id}/default")
            ->assertOk()
            ->assertJsonPath('data.id', $newDefaultPet->id)
            ->assertJsonPath('data.is_default', true);

        $oldDefaultPet->refresh();
        $newDefaultPet->refresh();

        $this->assertFalse($oldDefaultPet->is_default);
        $this->assertTrue($newDefaultPet->is_default);
    }
}
