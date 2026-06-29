<?php

namespace ClearAnalytics\Filament\Widgets\Goals;

use ClearAnalytics\Filament\Facades\ClearAnalytics;
use ClearAnalytics\Filament\Widgets\Concerns\AnalyticsTableWidget;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;

class GoalsWidget extends AnalyticsTableWidget
{
    protected function heading(): string
    {
        return (string) __('clear-analytics::clear-analytics.widgets.goals');
    }

    /**
     * @return array<Column>
     */
    protected function columns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__('clear-analytics::clear-analytics.columns.goal'))
                ->wrap(),
            TextColumn::make('type')
                ->label(__('clear-analytics::clear-analytics.columns.type'))
                ->badge(),
            TextColumn::make('is_active')
                ->label(__('clear-analytics::clear-analytics.columns.status'))
                ->badge()
                ->formatStateUsing(fn (mixed $state): string => $state
                    ? (string) __('clear-analytics::clear-analytics.goals.active')
                    : (string) __('clear-analytics::clear-analytics.goals.inactive'))
                ->color(fn (mixed $state): string => $state ? 'success' : 'gray'),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function rows(): array
    {
        return ClearAnalytics::goals();
    }
}
