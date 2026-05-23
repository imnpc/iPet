<?php

namespace App\Filament\Clusters\Pet\Resources\PetSpeciesResource\Pages;

use App\Filament\Clusters\Pet\Resources\PetSpeciesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPetSpecies extends ListRecords
{
    protected static string $resource = PetSpeciesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
