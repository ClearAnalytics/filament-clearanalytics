<?php

namespace ClearAnalytics\Filament\Widgets\Concerns;

use ClearAnalytics\Filament\Concerns\InteractsWithClearAnalyticsFilters;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Collection;

/**
 * Base for the breakdown table widgets (top pages, referrers, browsers, …).
 *
 * Subclasses provide a heading, the column set, and the rows (already fetched
 * from the API as plain arrays). Records are array-based — no Eloquent model.
 */
abstract class AnalyticsTableWidget extends TableWidget
{
    use InteractsWithClearAnalyticsFilters;
    use InteractsWithPageFilters;

    protected ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 1;

    abstract protected function heading(): string;

    /**
     * @return array<Column>
     */
    abstract protected function columns(): array;

    /**
     * @return list<array<string, mixed>>
     */
    abstract protected function rows(): array;

    public function table(Table $table): Table
    {
        return $table
            ->heading($this->heading())
            ->records(fn (): Collection => collect($this->rows()))
            ->columns($this->columns())
            ->paginated(false)
            ->emptyStateHeading(__('clear-analytics::clear-analytics.empty.heading'))
            ->emptyStateIcon('heroicon-o-chart-bar');
    }
}
