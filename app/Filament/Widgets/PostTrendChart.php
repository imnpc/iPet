<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Widgets\ChartWidget;

class PostTrendChart extends ChartWidget
{
    protected ?string $heading = null;

    protected ?string $pollingInterval = null;

    public function getHeading(): ?string
    {
        return trans('filament-model.widgets.post.trend_30_days');
    }

    protected function getData(): array
    {
        $days = collect(range(29, 0))->map(fn (int $daysAgo): string => now()->subDays($daysAgo)->format('m-d'));

        $counts = collect(range(29, 0))->map(function (int $daysAgo): int {
            $date = now()->subDays($daysAgo)->format('Y-m-d');

            return Post::whereDate('published_at', $date)->count();
        });

        return [
            'datasets' => [
                [
                    'label' => trans('filament-model.widgets.post.publish_count'),
                    'data' => $counts->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $days->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
