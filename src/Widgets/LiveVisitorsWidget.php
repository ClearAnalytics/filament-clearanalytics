<?php

namespace ClearAnalytics\Filament\Widgets;

use ClearAnalytics\Filament\Concerns\FormatsMetrics;
use ClearAnalytics\Filament\Concerns\InteractsWithClearAnalyticsFilters;
use ClearAnalytics\Filament\Facades\ClearAnalytics;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LiveVisitorsWidget extends StatsOverviewWidget
{
    use FormatsMetrics;
    use InteractsWithClearAnalyticsFilters;
    use InteractsWithPageFilters;

    protected ?string $pollingInterval = '30s';

    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 2;
    }

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $live = ClearAnalytics::live($this->caParams());
        $window = (int) ($live['window_minutes'] ?? 5);

        return [
            Stat::make(
                __('clear-analytics::clear-analytics.metrics.active_visitors'),
                $this->formatNumber((float) ($live['active_visitors'] ?? 0)),
            )
                ->description(__('clear-analytics::clear-analytics.live.window', ['minutes' => $window]))
                ->descriptionIcon('heroicon-m-signal')
                ->color('success'),

            Stat::make(
                __('clear-analytics::clear-analytics.metrics.pageviews'),
                $this->formatNumber((float) ($live['pageviews'] ?? 0)),
            )
                ->description(__('clear-analytics::clear-analytics.live.window', ['minutes' => $window]))
                ->descriptionIcon('heroicon-m-eye')
                ->color('primary'),
        ];
    }
}
