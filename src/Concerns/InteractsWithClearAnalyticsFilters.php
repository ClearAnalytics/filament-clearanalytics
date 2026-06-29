<?php

namespace ClearAnalytics\Filament\Concerns;

/**
 * Builds the API query params (period / date range / site) for a widget from the
 * dashboard page filters, falling back to config defaults when the widget is
 * rendered outside the dedicated dashboard (e.g. on the panel's own dashboard).
 *
 * Requires the using class to also use Filament's InteractsWithPageFilters
 * (which provides the `$pageFilters` property).
 */
trait InteractsWithClearAnalyticsFilters
{
    /**
     * @return array<string, mixed>
     */
    protected function caParams(): array
    {
        /** @var array<string, mixed> $filters */
        $filters = $this->pageFilters ?? [];

        $params = [];

        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;

        if (! empty($from) && ! empty($to)) {
            // An explicit date range overrides the named period.
            $params['from'] = $from;
            $params['to'] = $to;
        } else {
            $params['period'] = $filters['period'] ?? config('clear-analytics.period', '7d');
        }

        $siteId = $filters['site_id'] ?? config('clear-analytics.site_id');

        if (! empty($siteId)) {
            $params['site_id'] = $siteId;
        }

        return $params;
    }

    protected function caPeriodLabel(): string
    {
        /** @var array<string, mixed> $filters */
        $filters = $this->pageFilters ?? [];

        if (! empty($filters['from']) && ! empty($filters['to'])) {
            return $filters['from'].' – '.$filters['to'];
        }

        $period = $filters['period'] ?? config('clear-analytics.period', '7d');

        return (string) __('clear-analytics::clear-analytics.periods.'.$period);
    }
}
