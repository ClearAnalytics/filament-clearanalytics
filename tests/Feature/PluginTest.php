<?php

use ClearAnalytics\Filament\ClearAnalyticsPlugin;
use ClearAnalytics\Filament\Widgets;

it('has a stable id', function () {
    expect(ClearAnalyticsPlugin::make()->getId())->toBe('clear-analytics');
});

it('includes core widgets on the dedicated dashboard', function () {
    $widgets = ClearAnalyticsPlugin::dashboardWidgets();

    expect($widgets)->toContain(Widgets\OverviewStatsWidget::class)
        ->toContain(Widgets\TopPagesWidget::class)
        ->toContain(Widgets\VisitorsChartWidget::class);
});

it('omits a feature group when it is disabled', function () {
    config(['clear-analytics.features.ecommerce' => false]);

    $widgets = ClearAnalyticsPlugin::dashboardWidgets();

    expect($widgets)->not->toContain(Widgets\Ecommerce\RevenueStatsWidget::class)
        ->and($widgets)->toContain(Widgets\OverviewStatsWidget::class);
});

it('fluent setters write through to config', function () {
    ClearAnalyticsPlugin::make()
        ->baseUrl('https://example.test/api/v1')
        ->token('abc')
        ->site('01JSITE')
        ->defaultPeriod('30d')
        ->ecommerce(false);

    expect(config('clear-analytics.base_url'))->toBe('https://example.test/api/v1')
        ->and(config('clear-analytics.token'))->toBe('abc')
        ->and(config('clear-analytics.site_id'))->toBe('01JSITE')
        ->and(config('clear-analytics.period'))->toBe('30d')
        ->and(config('clear-analytics.features.ecommerce'))->toBeFalse();
});
