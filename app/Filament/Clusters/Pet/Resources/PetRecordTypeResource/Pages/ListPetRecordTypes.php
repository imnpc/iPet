<?php

namespace App\Filament\Clusters\Pet\Resources\PetRecordTypeResource\Pages;

use App\Filament\Clusters\Pet\Resources\PetRecordTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPetRecordTypes extends ListRecords
{
    protected static string $resource = PetRecordTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
