<?php

namespace App\Filament\Clusters\Pet\Resources\PetRecordResource\Pages;

use App\Filament\Clusters\Pet\Resources\PetRecordResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPetRecords extends ListRecords
{
    protected static string $resource = PetRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
