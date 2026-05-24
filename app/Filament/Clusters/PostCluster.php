<?php

namespace App\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class PostCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChatBubbleLeftRight;

    protected static ?int $navigationSort = 12;

    public static function getNavigationLabel(): string
    {
        return '动态';
    }

    public static function getClusterBreadcrumb(): string
    {
        return '动态';
    }
}
