<?php

namespace App\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class MedicalCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static ?int $navigationSort = 12;

    public static function getNavigationLabel(): string
    {
        return '医疗';
    }

    public static function getClusterBreadcrumb(): string
    {
        return '医疗';
    }
}
