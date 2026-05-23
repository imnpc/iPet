<?php

namespace App\Filament\Widgets;

use App\Models\Pet;
use Filament\Widgets\ChartWidget;

class PetWeightChart extends ChartWidget
{
    protected ?string $heading = null;

    protected ?string $pollingInterval = null;

    public ?Pet $record = null;

    public function getHeading(): ?string
    {
        return trans('filament-model.widgets.pet.weight_trend');
    }

    protected function getData(): array
    {
        $pet = $this->record;

        if (! $pet) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $records = $pet->records()
            ->whereNotNull('weight')
            ->orderBy('visit_date', 'asc')
            ->get(['visit_date', 'weight']);

        return [
            'datasets' => [
                [
                    'label' => trans('filament-model.widgets.pet.weight_kg'),
                    'data' => $records->pluck('weight')->map(fn ($w) => (float) $w)->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $records->pluck('visit_date')->map(fn ($d) => $d->format('Y-m-d'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
