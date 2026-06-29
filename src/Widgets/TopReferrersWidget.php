<?php

namespace ClearAnalytics\Filament\Widgets;

use ClearAnalytics\Filament\Facades\ClearAnalytics;
use ClearAnalytics\Filament\Widgets\Concerns\AnalyticsTableWidget;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;

class TopReferrersWidget extends AnalyticsTableWidget
{
    protected function heading(): string
    {
        return (string) __('clear-analytics::clear-analytics.widgets.top_referrers');
    }

    /**
     * @return array<Column>
     */
    protected function columns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__('clear-analytics::clear-analytics.columns.referrer'))
                ->placeholder(__('clear-analytics::clear-analytics.columns.direct'))
                ->wrap(),
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
        return ClearAnalytics::referrers($this->caParams() + ['limit' => 10]);
    }
}
