<?php

namespace ClearAnalytics\Filament\Widgets;

use ClearAnalytics\Filament\Concerns\FormatsMetrics;
use ClearAnalytics\Filament\Concerns\InteractsWithClearAnalyticsFilters;
use ClearAnalytics\Filament\Facades\ClearAnalytics;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OverviewStatsWidget extends StatsOverviewWidget
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
        $overview = ClearAnalytics::overview($params);
        $series = collect(ClearAnalytics::timeseries($params));

        $visitorsChart = $series->pluck('visitors')->map(fn (mixed $v): float => (float) $v)->all();
        $pageviewsChart = $series->pluck('pageviews')->map(fn (mixed $v): float => (float) $v)->all();

        return [
            Stat::make(
                __('clear-analytics::clear-analytics.metrics.visitors'),
                $this->formatNumber((float) $overview['visitors']),
            )
                ->description($this->changeDescription((float) $overview['visitors_change']))
                ->descriptionIcon($this->changeIcon((float) $overview['visitors_change']), IconPosition::Before)
                ->color($this->changeColor((float) $overview['visitors_change']))
                ->chart($visitorsChart ?: [0.0, 0.0]),

            Stat::make(
                __('clear-analytics::clear-analytics.metrics.pageviews'),
                $this->formatNumber((float) $overview['pageviews']),
            )
                ->description($this->changeDescription((float) $overview['pageviews_change']))
                ->descriptionIcon($this->changeIcon((float) $overview['pageviews_change']), IconPosition::Before)
                ->color($this->changeColor((float) $overview['pageviews_change']))
                ->chart($pageviewsChart ?: [0.0, 0.0]),

            Stat::make(
                __('clear-analytics::clear-analytics.metrics.bounce_rate'),
                $this->formatPercent((float) $overview['bounce_rate']),
            )
                ->description($this->changeDescription((float) $overview['bounce_rate_change']))
                ->descriptionIcon($this->changeIcon((float) $overview['bounce_rate_change']), IconPosition::Before)
                ->color($this->changeColor((float) $overview['bounce_rate_change'], higherIsBetter: false)),

            Stat::make(
                __('clear-analytics::clear-analytics.metrics.avg_duration'),
                $this->formatDuration((float) $overview['avg_session_duration']),
            )
                ->description($this->changeDescription((float) $overview['avg_session_duration_change']))
                ->descriptionIcon($this->changeIcon((float) $overview['avg_session_duration_change']), IconPosition::Before)
                ->color($this->changeColor((float) $overview['avg_session_duration_change'])),
        ];
    }
}
