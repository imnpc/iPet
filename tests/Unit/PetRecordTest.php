<?php

namespace Tests\Unit;

use App\Models\Pet;
use App\Models\PetRecord;
use Tests\TestCase;

class PetRecordTest extends TestCase
{
    public function test_can_create_a_pet_record(): void
    {
        $pet = Pet::factory()->create();
        $record = PetRecord::factory()->create([
            'pet_id' => $pet->id,
            'type' => 'vaccine',
            'title' => '狂犬疫苗',
            'visit_date' => '2026-01-15',
        ]);

        $this->assertEquals('vaccine', $record->type);
        $this->assertEquals('狂犬疫苗', $record->title);
        $this->assertEquals($pet->id, $record->pet_id);
    }

    public function test_belongs_to_a_pet(): void
    {
        $pet = Pet::factory()->create();
        $record = PetRecord::factory()->create(['pet_id' => $pet->id]);

        $this->assertInstanceOf(Pet::class, $record->pet);
        $this->assertEquals($pet->id, $record->pet->id);
    }

    public function test_supports_soft_delete(): void
    {
        $record = PetRecord::factory()->create();
        $record->delete();
        $this->assertSoftDeleted('pet_records', ['id' => $record->id]);
    }
}
