<?php

namespace App\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class CommentCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChatBubbleOvalLeftEllipsis;

    protected static ?int $navigationSort = 13;

    public static function getNavigationLabel(): string
    {
        return '评论';
    }

    public static function getClusterBreadcrumb(): string
    {
        return '评论';
    }
}
