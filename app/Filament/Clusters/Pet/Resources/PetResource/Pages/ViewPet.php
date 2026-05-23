<?php

namespace App\Filament\Clusters\Pet\Resources\PetResource\Pages;

use App\Filament\Clusters\Pet\Resources\PetResource;
use App\Filament\Widgets\PetWeightChart;
use Filament\Resources\Pages\ViewRecord;

class ViewPet extends ViewRecord
{
    protected static string $resource = PetResource::class;

    public function getHeaderWidgets(): array
    {
        return [
            PetWeightChart::class,
        ];
    }
}
