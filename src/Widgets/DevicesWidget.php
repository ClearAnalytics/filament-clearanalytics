<?php

namespace ClearAnalytics\Filament\Widgets;

use ClearAnalytics\Filament\Concerns\InteractsWithClearAnalyticsFilters;
use ClearAnalytics\Filament\Facades\ClearAnalytics;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;

class DevicesWidget extends ChartWidget
{
    use InteractsWithClearAnalyticsFilters;
    use InteractsWithPageFilters;

    protected ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '300px';

    public function getHeading(): string|Htmlable|null
    {
        return (string) __('clear-analytics::clear-analytics.widgets.devices');
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $devices = collect(ClearAnalytics::devices($this->caParams()));

        return [
            'datasets' => [
                [
                    'label' => (string) __('clear-analytics::clear-analytics.metrics.visitors'),
                    'data' => $devices->pluck('visitors')->map(fn (mixed $v): int => (int) $v)->all(),
                    'backgroundColor' => [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(249, 115, 22)',
                        'rgb(168, 85, 247)',
                    ],
                ],
            ],
            'labels' => $devices->pluck('device')
                ->map(fn (mixed $d): string => ucfirst((string) $d))
                ->all(),
        ];
    }
}
