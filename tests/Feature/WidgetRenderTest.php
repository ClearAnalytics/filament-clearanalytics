<?php

use ClearAnalytics\Filament\Pages\ClearAnalyticsDashboard;
use ClearAnalytics\Filament\Widgets\OverviewStatsWidget;
use ClearAnalytics\Filament\Widgets\TopPagesWidget;
use Filament\Facades\Filament;
use Filament\Tables\Table;

beforeEach(function () {
    Filament::setCurrentPanel('admin');
});

it('registers the dedicated dashboard page on the panel', function () {
    $pages = Filament::getPanel('admin')->getPages();

    expect($pages)->toContain(ClearAnalyticsDashboard::class);
});

it('exposes the dashboard page at its own route path', function () {
    expect(ClearAnalyticsDashboard::getRoutePath(Filament::getPanel('admin')))->toBe('clear-analytics');
});

it('builds a configured table for a breakdown widget', function () {
    $widget = app(TopPagesWidget::class);
    $table = $widget->table(Table::make($widget));

    expect($table->getColumns())->toHaveCount(3)
        ->and(array_keys($table->getColumns()))->toContain('name', 'visitors', 'pageviews');
});

it('resolves the overview widget through the container', function () {
    expect(app(OverviewStatsWidget::class))->toBeInstanceOf(OverviewStatsWidget::class);
});
