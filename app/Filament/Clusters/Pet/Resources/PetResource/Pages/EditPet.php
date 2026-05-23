<?php

namespace App\Filament\Clusters\Pet\Resources\PetResource\Pages;

use App\Filament\Clusters\Pet\Resources\PetResource;
use Filament\Resources\Pages\EditRecord;

class EditPet extends EditRecord
{
    protected static string $resource = PetResource::class;
}
