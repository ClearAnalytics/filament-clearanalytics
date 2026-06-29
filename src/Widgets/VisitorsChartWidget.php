<?php

namespace ClearAnalytics\Filament\Widgets;

use ClearAnalytics\Filament\Concerns\InteractsWithClearAnalyticsFilters;
use ClearAnalytics\Filament\Facades\ClearAnalytics;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;

class VisitorsChartWidget extends ChartWidget
{
    use InteractsWithClearAnalyticsFilters;
    use InteractsWithPageFilters;

    protected ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '300px';

    public function getHeading(): string|Htmlable|null
    {
        return (string) __('clear-analytics::clear-analytics.widgets.visitors_chart');
    }

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $series = collect(ClearAnalytics::timeseries($this->caParams()));

        return [
            'datasets' => [
                [
                    'label' => (string) __('clear-analytics::clear-analytics.metrics.visitors'),
                    'data' => $series->pluck('visitors')->map(fn (mixed $v): int => (int) $v)->all(),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => (string) __('clear-analytics::clear-analytics.metrics.pageviews'),
                    'data' => $series->pluck('pageviews')->map(fn (mixed $v): int => (int) $v)->all(),
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $series->pluck('date')->all(),
        ];
    }
}
