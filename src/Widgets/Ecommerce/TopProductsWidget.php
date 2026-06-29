<?php

namespace ClearAnalytics\Filament\Widgets\Ecommerce;

use ClearAnalytics\Filament\Facades\ClearAnalytics;
use ClearAnalytics\Filament\Widgets\Concerns\AnalyticsTableWidget;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;

class TopProductsWidget extends AnalyticsTableWidget
{
    protected function heading(): string
    {
        return (string) __('clear-analytics::clear-analytics.widgets.ecommerce_top_products');
    }

    /**
     * @return array<Column>
     */
    protected function columns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__('clear-analytics::clear-analytics.columns.product'))
                ->wrap(),
            TextColumn::make('revenue')
                ->label(__('clear-analytics::clear-analytics.columns.revenue'))
                ->numeric(decimalPlaces: 2)
                ->alignEnd(),
            TextColumn::make('quantity')
                ->label(__('clear-analytics::clear-analytics.columns.quantity'))
                ->numeric()
                ->alignEnd(),
            TextColumn::make('transactions')
                ->label(__('clear-analytics::clear-analytics.columns.transactions'))
                ->numeric()
                ->alignEnd(),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function rows(): array
    {
        return ClearAnalytics::ecommerceProducts($this->caParams() + ['limit' => 10]);
    }
}
