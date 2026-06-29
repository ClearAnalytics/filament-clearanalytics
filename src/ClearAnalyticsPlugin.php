<?php

namespace ClearAnalytics\Filament;

use ClearAnalytics\Filament\Pages\ClearAnalyticsDashboard;
use Filament\Contracts\Plugin;
use Filament\Panel;

class ClearAnalyticsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'clear-analytics';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function register(Panel $panel): void
    {
        if (config('clear-analytics.dashboard.enabled', true)) {
            $panel->pages([
                ClearAnalyticsDashboard::class,
            ]);
        }

        if (config('clear-analytics.register_on_dashboard', false)) {
            $panel->widgets($this->panelWidgets());
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Fluent configuration (overrides config/clear-analytics.php per panel)
    |--------------------------------------------------------------------------
    */

    public function baseUrl(string $url): static
    {
        config(['clear-analytics.base_url' => $url]);

        return $this;
    }

    public function token(?string $token): static
    {
        config(['clear-analytics.token' => $token]);

        return $this;
    }

    public function site(?string $siteId): static
    {
        config(['clear-analytics.site_id' => $siteId]);

        return $this;
    }

    public function defaultPeriod(string $period): static
    {
        config(['clear-analytics.period' => $period]);

        return $this;
    }

    public function cacheTtl(int $seconds): static
    {
        config(['clear-analytics.cache_ttl' => $seconds]);

        return $this;
    }

    public function ecommerce(bool $enabled = true): static
    {
        config(['clear-analytics.features.ecommerce' => $enabled]);

        return $this;
    }

    public function goals(bool $enabled = true): static
    {
        config(['clear-analytics.features.goals' => $enabled]);

        return $this;
    }

    public function live(bool $enabled = true): static
    {
        config(['clear-analytics.features.live' => $enabled]);

        return $this;
    }

    public function registerOnDashboard(bool $enabled = true): static
    {
        config(['clear-analytics.register_on_dashboard' => $enabled]);

        return $this;
    }

    /**
     * @param  array<string, bool>  $widgets
     */
    public function widgets(array $widgets): static
    {
        config(['clear-analytics.widgets' => array_merge(
            (array) config('clear-analytics.widgets', []),
            $widgets,
        )]);

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Widget resolution
    |--------------------------------------------------------------------------
    */

    /**
     * The full ordered widget map: config key => [class, feature group].
     * A null feature means the widget is part of the always-on core.
     *
     * @return array<string, array{class: class-string, feature: string|null}>
     */
    public static function widgetMap(): array
    {
        return [
            'overview_stats' => ['class' => Widgets\OverviewStatsWidget::class, 'feature' => null],
            'live_visitors' => ['class' => Widgets\LiveVisitorsWidget::class, 'feature' => 'live'],
            'visitors_chart' => ['class' => Widgets\VisitorsChartWidget::class, 'feature' => null],
            'top_pages' => ['class' => Widgets\TopPagesWidget::class, 'feature' => null],
            'top_referrers' => ['class' => Widgets\TopReferrersWidget::class, 'feature' => null],
            'traffic_sources' => ['class' => Widgets\TrafficSourcesWidget::class, 'feature' => null],
            'devices' => ['class' => Widgets\DevicesWidget::class, 'feature' => null],
            'browsers' => ['class' => Widgets\BrowsersWidget::class, 'feature' => null],
            'operating_systems' => ['class' => Widgets\OperatingSystemsWidget::class, 'feature' => null],
            'languages' => ['class' => Widgets\LanguagesWidget::class, 'feature' => null],
            'campaigns' => ['class' => Widgets\CampaignsWidget::class, 'feature' => null],
            'ecommerce_revenue_stats' => ['class' => Widgets\Ecommerce\RevenueStatsWidget::class, 'feature' => 'ecommerce'],
            'ecommerce_revenue_chart' => ['class' => Widgets\Ecommerce\RevenueChartWidget::class, 'feature' => 'ecommerce'],
            'ecommerce_top_products' => ['class' => Widgets\Ecommerce\TopProductsWidget::class, 'feature' => 'ecommerce'],
            'ecommerce_funnel' => ['class' => Widgets\Ecommerce\FunnelWidget::class, 'feature' => 'ecommerce'],
            'goals' => ['class' => Widgets\Goals\GoalsWidget::class, 'feature' => 'goals'],
        ];
    }

    /**
     * Widgets shown on the dedicated dashboard: every widget whose feature group
     * is enabled (the per-widget booleans only gate the panel dashboard).
     *
     * @return list<class-string>
     */
    public static function dashboardWidgets(): array
    {
        $widgets = [];

        foreach (static::widgetMap() as $config) {
            if (static::featureEnabled($config['feature'])) {
                $widgets[] = $config['class'];
            }
        }

        return $widgets;
    }

    /**
     * Widgets pushed onto the panel's own dashboard: enabled feature AND the
     * per-widget boolean toggle.
     *
     * @return list<class-string>
     */
    protected function panelWidgets(): array
    {
        $widgets = [];

        foreach (static::widgetMap() as $key => $config) {
            if (! static::featureEnabled($config['feature'])) {
                continue;
            }

            if (config('clear-analytics.widgets.'.$key, false)) {
                $widgets[] = $config['class'];
            }
        }

        return $widgets;
    }

    protected static function featureEnabled(?string $feature): bool
    {
        if ($feature === null) {
            return true;
        }

        return (bool) config('clear-analytics.features.'.$feature, true);
    }
}
