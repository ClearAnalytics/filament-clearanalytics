<?php

namespace ClearAnalytics\Filament\Widgets\Ecommerce;

use ClearAnalytics\Filament\Facades\ClearAnalytics;
use ClearAnalytics\Filament\Widgets\Concerns\AnalyticsTableWidget;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;

class FunnelWidget extends AnalyticsTableWidget
{
    protected function heading(): string
    {
        return (string) __('clear-analytics::clear-analytics.widgets.ecommerce_funnel');
    }

    /**
     * @return array<Column>
     */
    protected function columns(): array
    {
        return [
            TextColumn::make('label')
                ->label(__('clear-analytics::clear-analytics.columns.funnel_step')),
            TextColumn::make('visitors')
                ->label(__('clear-analytics::clear-analytics.columns.visitors'))
                ->numeric()
                ->alignEnd(),
            TextColumn::make('dropoff_rate')
                ->label(__('clear-analytics::clear-analytics.columns.dropoff'))
                ->formatStateUsing(fn (mixed $state): string => number_format((float) $state, 1).'%')
                ->badge()
                ->color(fn (mixed $state): string => ((float) $state) > 50 ? 'danger' : 'gray')
                ->alignEnd(),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function rows(): array
    {
        return ClearAnalytics::ecommerceFunnel($this->caParams());
    }
}
