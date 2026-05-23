<?php

namespace Tests\Feature\Api;

use App\Models\Pet;
use App\Models\PetRecord;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PetRecordApiTest extends TestCase
{
    public function test_can_list_pet_records(): void
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $user->id]);
        PetRecord::factory()->count(3)->create(['pet_id' => $pet->id]);

        Sanctum::actingAs($user);
        $response = $this->getJson("/api/v1/records?pet_id={$pet->id}");
        $response->assertOk()->assertJsonPath('status', 'success');
    }

    public function test_can_create_a_pet_record(): void
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);
        $response = $this->postJson('/api/v1/records', [
            'pet_id' => $pet->id,
            'type' => 'vaccine',
            'title' => '狂犬疫苗',
            'visit_date' => '2026-01-15',
            'next_visit_date' => '2027-01-15',
        ]);

        $response->assertCreated()->assertJsonPath('data.title', '狂犬疫苗');

        $this->assertDatabaseHas('pet_records', ['title' => '狂犬疫苗', 'pet_id' => $pet->id]);
    }

    public function test_cannot_create_record_for_others_pet(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $user2->id]);

        Sanctum::actingAs($user1);
        $response = $this->postJson('/api/v1/records', [
            'pet_id' => $pet->id,
            'type' => 'vaccine',
            'title' => 'test',
            'visit_date' => '2026-01-15',
        ]);

        $response->assertNotFound();
    }
}
