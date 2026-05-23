<?php

namespace App\Filament\Clusters\Pet;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class PetCluster extends Cluster
{
    use HasPageShield;

    public static function getClusterBreadcrumb(): string
    {
        return __('filament-model.navigation_group.pet.name');
    }

    public static function getNavigationIcon(): BackedEnum|Htmlable|string|null
    {
        return Heroicon::Heart;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-model.navigation_group.pet.name');
    }

    public static function getNavigationSort(): ?int
    {
        return 15;
    }

    public function getTitle(): string
    {
        return __('filament-model.navigation_group.pet.name');
    }
}
