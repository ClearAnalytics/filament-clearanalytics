<?php

namespace ClearAnalytics\Filament\Widgets\Ecommerce;

use ClearAnalytics\Filament\Concerns\FormatsMetrics;
use ClearAnalytics\Filament\Concerns\InteractsWithClearAnalyticsFilters;
use ClearAnalytics\Filament\Facades\ClearAnalytics;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RevenueStatsWidget extends StatsOverviewWidget
{
    use FormatsMetrics;
    use InteractsWithClearAnalyticsFilters;
    use InteractsWithPageFilters;

    protected ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 4;
    }

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $params = $this->caParams();
        $overview = ClearAnalytics::ecommerceOverview($params);
        $series = collect(ClearAnalytics::ecommerceRevenueTimeseries($params));

        $revenueChart = $series->pluck('revenue')->map(fn (mixed $v): float => (float) $v)->all();

        return [
            Stat::make(
                __('clear-analytics::clear-analytics.metrics.revenue'),
                $this->formatMoney((float) $overview['revenue']),
            )
                ->description($this->changeDescription((float) $overview['revenue_change']))
                ->descriptionIcon($this->changeIcon((float) $overview['revenue_change']), IconPosition::Before)
                ->color($this->changeColor((float) $overview['revenue_change']))
                ->chart($revenueChart ?: [0.0, 0.0]),

            Stat::make(
                __('clear-analytics::clear-analytics.metrics.transactions'),
                $this->formatNumber((float) $overview['transactions']),
            )
                ->description($this->changeDescription((float) $overview['transactions_change']))
                ->descriptionIcon($this->changeIcon((float) $overview['transactions_change']), IconPosition::Before)
                ->color($this->changeColor((float) $overview['transactions_change'])),

            Stat::make(
                __('clear-analytics::clear-analytics.metrics.avg_order_value'),
                $this->formatMoney((float) $overview['average_order_value']),
            )->color('primary'),

            Stat::make(
                __('clear-analytics::clear-analytics.metrics.conversion_rate'),
                $this->formatPercent((float) $overview['conversion_rate']),
            )->color('primary'),
        ];
    }
}
