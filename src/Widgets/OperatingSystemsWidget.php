<?php

namespace ClearAnalytics\Filament\Widgets;

use ClearAnalytics\Filament\Facades\ClearAnalytics;
use ClearAnalytics\Filament\Widgets\Concerns\AnalyticsTableWidget;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;

class OperatingSystemsWidget extends AnalyticsTableWidget
{
    protected function heading(): string
    {
        return (string) __('clear-analytics::clear-analytics.widgets.operating_systems');
    }

    /**
     * @return array<Column>
     */
    protected function columns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__('clear-analytics::clear-analytics.columns.operating_system')),
            TextColumn::make('visitors')
                ->label(__('clear-analytics::clear-analytics.columns.visitors'))
                ->numeric()
                ->alignEnd(),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function rows(): array
    {
        return ClearAnalytics::operatingSystems($this->caParams() + ['limit' => 10]);
    }
}
