<?php

namespace App\Filament\Clusters\Pet\Resources\PetSpeciesResource\Pages;

use App\Filament\Clusters\Pet\Resources\PetSpeciesResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePetSpecies extends CreateRecord
{
    protected static string $resource = PetSpeciesResource::class;
}
