<?php

namespace ClearAnalytics\Filament\Pages;

use BackedEnum;
use ClearAnalytics\Filament\ClearAnalyticsPlugin;
use ClearAnalytics\Filament\Facades\ClearAnalytics;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use UnitEnum;

class ClearAnalyticsDashboard extends Dashboard
{
    use HasFiltersForm;

    protected static string $routePath = 'clear-analytics';

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return config('clear-analytics.dashboard.icon', 'heroicon-o-chart-bar');
    }

    public static function getNavigationSort(): ?int
    {
        return config('clear-analytics.dashboard.navigation_sort', 2);
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return config('clear-analytics.dashboard.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return (string) __('clear-analytics::clear-analytics.dashboard.title');
    }

    public function getTitle(): string|Htmlable
    {
        return (string) __('clear-analytics::clear-analytics.dashboard.title');
    }

    public function getSubheading(): string|Htmlable|null
    {
        // Product attribution — intentionally not translated (brand name).
        return new HtmlString(
            '<a href="https://clearanalytics.eu" target="_blank" rel="noopener" '
            .'class="inline-flex items-center gap-1 font-medium text-primary-600 hover:underline dark:text-primary-400">'
            .'🇪🇺 ClearAnalytics.eu</a>'
        );
    }

    /**
     * @return int|array<string, ?int>
     */
    public function getColumns(): int|array
    {
        return min((int) config('clear-analytics.dashboard.columns', 2), 3);
    }

    /**
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return ClearAnalyticsPlugin::dashboardWidgets();
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('period')
                ->label(__('clear-analytics::clear-analytics.filters.period'))
                ->options($this->periodOptions())
                ->default(config('clear-analytics.period', '7d'))
                ->selectablePlaceholder(false)
                ->native(false),

            Select::make('site_id')
                ->label(__('clear-analytics::clear-analytics.filters.site'))
                ->options($this->siteOptions())
                ->default(config('clear-analytics.site_id'))
                ->placeholder(__('clear-analytics::clear-analytics.filters.all_sites'))
                ->native(false),

            DatePicker::make('from')
                ->label(__('clear-analytics::clear-analytics.filters.from'))
                ->native(false)
                ->maxDate(now()),

            DatePicker::make('to')
                ->label(__('clear-analytics::clear-analytics.filters.to'))
                ->native(false)
                ->maxDate(now()),
        ]);
    }

    /**
     * @return array<string, string>
     */
    protected function periodOptions(): array
    {
        return collect(['24h', '7d', '30d', '90d', '12m'])
            ->mapWithKeys(fn (string $period): array => [
                $period => (string) __('clear-analytics::clear-analytics.periods.'.$period),
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    protected function siteOptions(): array
    {
        return collect(ClearAnalytics::sites())
            ->mapWithKeys(fn (array $site): array => [
                (string) ($site['id'] ?? '') => (string) ($site['domain'] ?? $site['id'] ?? ''),
            ])
            ->filter(fn (string $label, string $id): bool => $id !== '')
            ->all();
    }
}
