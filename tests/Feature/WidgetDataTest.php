<?php

use ClearAnalytics\Filament\Widgets\OverviewStatsWidget;
use ClearAnalytics\Filament\Widgets\TopPagesWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Http;

/**
 * Invoke a protected method on a widget instance without booting Livewire.
 */
function callProtected(object $object, string $method): mixed
{
    $closure = Closure::bind(fn () => $this->{$method}(), $object, $object::class);

    return $closure();
}

it('builds four stats from the overview and timeseries endpoints', function () {
    Http::fake([
        '*/stats/overview*' => Http::response(['data' => [
            'visitors' => 1523, 'pageviews' => 4891, 'bounce_rate' => 42.3, 'avg_session_duration' => 187,
            'visitors_change' => 12.5, 'pageviews_change' => 8.2, 'bounce_rate_change' => -3.1, 'avg_session_duration_change' => 15.7,
        ]]),
        '*/stats/timeseries*' => Http::response(['data' => [
            ['date' => '2026-06-01', 'visitors' => 200, 'pageviews' => 500],
            ['date' => '2026-06-02', 'visitors' => 240, 'pageviews' => 560],
        ]]),
    ]);

    $stats = callProtected(new OverviewStatsWidget, 'getStats');

    expect($stats)->toHaveCount(4)
        ->and($stats[0])->toBeInstanceOf(Stat::class);
});

it('maps top pages rows from the API', function () {
    Http::fake([
        '*/stats/pages*' => Http::response(['data' => [
            ['name' => '/', 'visitors' => 890, 'pageviews' => 1234],
        ]]),
    ]);

    $rows = callProtected(new TopPagesWidget, 'rows');

    expect($rows)->toHaveCount(1)
        ->and($rows[0]['name'])->toBe('/')
        ->and($rows[0]['pageviews'])->toBe(1234);
});
