<?php

namespace App\Filament\Clusters\Pet\Resources\PetSpeciesResource\Pages;

use App\Filament\Clusters\Pet\Resources\PetSpeciesResource;
use Filament\Resources\Pages\EditRecord;

class EditPetSpecies extends EditRecord
{
    protected static string $resource = PetSpeciesResource::class;
}
