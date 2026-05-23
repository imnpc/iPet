<?php

namespace App\Filament\Clusters\Pet\Resources\PetResource\Pages;

use App\Filament\Clusters\Pet\Resources\PetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPets extends ListRecords
{
    protected static string $resource = PetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
