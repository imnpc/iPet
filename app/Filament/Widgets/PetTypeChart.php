<?php

namespace App\Filament\Widgets;

use App\Models\Pet;
use Filament\Widgets\ChartWidget;

class PetTypeChart extends ChartWidget
{
    protected ?string $heading = null;

    protected ?string $pollingInterval = null;

    public function getHeading(): ?string
    {
        return trans('filament-model.widgets.pet.type_distribution');
    }

    protected function getData(): array
    {
        $types = Pet::with('species')
            ->get()
            ->groupBy('species.name')
            ->map(fn ($group) => $group->count())
            ->sortDesc()
            ->take(10);

        $colors = [
            '#3b82f6',
            '#ef4444',
            '#10b981',
            '#f59e0b',
            '#8b5cf6',
            '#ec4899',
            '#06b6d4',
            '#84cc16',
            '#f97316',
            '#6366f1',
        ];

        return [
            'datasets' => [
                [
                    'label' => trans('filament-model.widgets.pet.count'),
                    'data' => $types->pluck('count')->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $types->count()),
                ],
            ],
            'labels' => $types->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
