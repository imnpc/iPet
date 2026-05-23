<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Pet;
use App\Models\PetRecord;
use App\Models\Post;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(trans('filament-model.widgets.stats.users_total'), User::count())
                ->description(trans('filament-model.widgets.stats.users_registered'))
                ->descriptionIcon('heroicon-o-users')
                ->color('primary'),

            Stat::make(trans('filament-model.widgets.stats.pets_total'), Pet::count())
                ->description(trans('filament-model.widgets.stats.pets_recorded'))
                ->descriptionIcon('heroicon-o-heart')
                ->color('danger'),

            Stat::make(trans('filament-model.widgets.stats.posts_total'), Post::whereNotNull('published_at')->count())
                ->description(trans('filament-model.widgets.stats.posts_published'))
                ->descriptionIcon('heroicon-o-chat-bubble-left-right')
                ->color('success'),

            Stat::make(trans('filament-model.widgets.stats.records_total'), PetRecord::count())
                ->description(trans('filament-model.widgets.stats.records_pet_medical'))
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color('warning'),

            Stat::make(trans('filament-model.widgets.stats.likes_total'), Like::count())
                ->description(trans('filament-model.widgets.stats.likes_total_count'))
                ->descriptionIcon('heroicon-o-hand-thumb-up')
                ->color('info'),

            Stat::make(trans('filament-model.widgets.stats.comments_total'), Comment::count())
                ->description(trans('filament-model.widgets.stats.comments_total_count'))
                ->descriptionIcon('heroicon-o-chat-bubble-oval-left')
                ->color('gray'),
        ];
    }
}
