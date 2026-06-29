<?php

namespace ClearAnalytics\Filament\Widgets\Ecommerce;

use ClearAnalytics\Filament\Concerns\InteractsWithClearAnalyticsFilters;
use ClearAnalytics\Filament\Facades\ClearAnalytics;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;

class RevenueChartWidget extends ChartWidget
{
    use InteractsWithClearAnalyticsFilters;
    use InteractsWithPageFilters;

    protected ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '300px';

    public function getHeading(): string|Htmlable|null
    {
        return (string) __('clear-analytics::clear-analytics.widgets.ecommerce_revenue_chart');
    }

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $series = collect(ClearAnalytics::ecommerceRevenueTimeseries($this->caParams()));

        return [
            'datasets' => [
                [
                    'label' => (string) __('clear-analytics::clear-analytics.metrics.revenue'),
                    'data' => $series->pluck('revenue')->map(fn (mixed $v): float => (float) $v)->all(),
                    'backgroundColor' => 'rgba(16, 185, 129, 0.5)',
                    'borderColor' => 'rgb(16, 185, 129)',
                ],
            ],
            'labels' => $series->pluck('date')->all(),
        ];
    }
}
