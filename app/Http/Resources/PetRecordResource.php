<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PetRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pet_id' => $this->pet_id,
            'type' => $this->type?->slug,
            'type_label' => $this->type?->name,
            'title' => $this->title,
            'visit_date' => $this->visit_date?->format('Y-m-d'),
            'next_visit_date' => $this->next_visit_date?->format('Y-m-d'),
            'hospital_name' => $this->hospital_name,
            'vet_name' => $this->vet_name,
            'hospital_phone' => $this->hospital_phone,
            'weight' => $this->weight,
            'temperature' => $this->temperature,
            'symptoms' => $this->symptoms,
            'diagnosis' => $this->diagnosis,
            'treatment' => $this->treatment,
            'prescription' => $this->prescription,
            'notes' => $this->notes,
            'cost' => $this->cost,
            'is_public' => $this->is_public,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
