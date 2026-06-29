<?php

// config for ClearAnalytics\Filament
return [

    /*
    |--------------------------------------------------------------------------
    | API connection
    |--------------------------------------------------------------------------
    |
    | The base URL of the ClearAnalytics REST API (v1) and the Bearer token used
    | to authenticate. Create a token under Settings → API Tokens in your
    | ClearAnalytics account. For self-hosted installs, override the base URL.
    |
    */

    'base_url' => env('CLEARANALYTICS_BASE_URL', 'https://clearanalytics.eu/api/v1'),

    'token' => env('CLEARANALYTICS_API_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Default site scope
    |--------------------------------------------------------------------------
    |
    | The default site (domain) to show stats for. Use the site's ULID id as
    | returned by GET /sites. Leave null to show the aggregate of every site
    | that opted in to the account-wide totals.
    |
    */

    'site_id' => env('CLEARANALYTICS_SITE_ID'),

    /*
    |--------------------------------------------------------------------------
    | Default period
    |--------------------------------------------------------------------------
    |
    | One of: 24h, 7d, 30d, 90d, 12m. Used as the initial value of the period
    | filter, and as the period for any widget rendered outside the dedicated
    | dashboard (e.g. on the panel's own dashboard).
    |
    */

    'period' => env('CLEARANALYTICS_PERIOD', '7d'),

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | API responses are cached to stay within ClearAnalytics' rate limit
    | (60 requests/minute per token). TTL is in seconds. Live/realtime data
    | uses its own short TTL.
    |
    */

    'cache_ttl' => (int) env('CLEARANALYTICS_CACHE_TTL', 300),

    'live_cache_ttl' => (int) env('CLEARANALYTICS_LIVE_CACHE_TTL', 10),

    /*
    |--------------------------------------------------------------------------
    | HTTP timeout (seconds)
    |--------------------------------------------------------------------------
    */

    'timeout' => (int) env('CLEARANALYTICS_HTTP_TIMEOUT', 10),

    /*
    |--------------------------------------------------------------------------
    | Dedicated dashboard page
    |--------------------------------------------------------------------------
    */

    'dashboard' => [
        'enabled' => true,
        'icon' => 'heroicon-o-chart-bar',
        'navigation_sort' => 2,
        'navigation_group' => null,
        // Number of grid columns on the dedicated dashboard page.
        'columns' => 2,
    ],

    /*
    |--------------------------------------------------------------------------
    | Optional feature sections
    |--------------------------------------------------------------------------
    |
    | Toggle the optional widget groups. Disabled groups are never registered.
    |
    */

    'features' => [
        'ecommerce' => true,
        'goals' => true,
        'live' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Register widgets on the panel's main dashboard
    |--------------------------------------------------------------------------
    |
    | When true, the widgets enabled below are also pushed onto the panel's own
    | dashboard (in addition to always appearing on the dedicated dashboard).
    | The booleans control which widgets are eligible.
    |
    */

    'register_on_dashboard' => false,

    'widgets' => [
        'overview_stats' => true,
        'visitors_chart' => true,
        'top_pages' => true,
        'top_referrers' => true,
        'traffic_sources' => false,
        'browsers' => false,
        'operating_systems' => false,
        'devices' => true,
        'languages' => false,
        'campaigns' => false,
        'live_visitors' => false,
        'ecommerce_revenue_stats' => false,
        'ecommerce_revenue_chart' => false,
        'ecommerce_top_products' => false,
        'ecommerce_funnel' => false,
        'goals' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Trend colours
    |--------------------------------------------------------------------------
    |
    | Colours used for the percentage-change indicators on stat cards.
    |
    */

    'trends' => [
        'up' => 'success',
        'down' => 'danger',
        'steady' => 'gray',
    ],
];
