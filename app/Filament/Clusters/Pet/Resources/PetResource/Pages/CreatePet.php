<?php

namespace App\Filament\Clusters\Pet\Resources\PetResource\Pages;

use App\Filament\Clusters\Pet\Resources\PetResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePet extends CreateRecord
{
    protected static string $resource = PetResource::class;
}
