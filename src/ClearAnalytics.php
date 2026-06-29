<?php

namespace ClearAnalytics\Filament;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Thin, cached client for the ClearAnalytics REST API (v1).
 *
 * Every method returns plain arrays already unwrapped from the API's
 * `{"data": ...}` envelope. Failures never throw into widgets — they are logged
 * and an empty/default value is returned so the dashboard degrades gracefully.
 */
class ClearAnalytics
{
    /**
     * Query parameters the API understands. Anything else is dropped.
     *
     * @var list<string>
     */
    protected array $allowedParams = ['period', 'from', 'to', 'limit', 'site_id', 'hostname', 'path'];

    public function baseUrl(): string
    {
        return rtrim((string) config('clear-analytics.base_url'), '/');
    }

    public function token(): ?string
    {
        $token = config('clear-analytics.token');

        return is_string($token) && $token !== '' ? $token : null;
    }

    public function isConfigured(): bool
    {
        return $this->token() !== null;
    }

    /*
    |--------------------------------------------------------------------------
    | Stats endpoints
    |--------------------------------------------------------------------------
    */

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function overview(array $params = []): array
    {
        return $this->getObject('stats/overview', $params, [
            'visitors' => 0,
            'pageviews' => 0,
            'bounce_rate' => 0,
            'avg_session_duration' => 0,
            'visitors_change' => 0,
            'pageviews_change' => 0,
            'bounce_rate_change' => 0,
            'avg_session_duration_change' => 0,
        ]);
    }

    /**
     * @param  array<string, mixed>  $params
     * @return list<array{date: string, visitors: int, pageviews: int}>
     */
    public function timeseries(array $params = []): array
    {
        return $this->getList('stats/timeseries', $params);
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function live(array $params = []): array
    {
        return $this->getObject('stats/live', $params, [
            'active_visitors' => 0,
            'pageviews' => 0,
            'window_minutes' => 5,
        ], ttl: (int) config('clear-analytics.live_cache_ttl', 10));
    }

    /**
     * @param  array<string, mixed>  $params
     * @return list<array<string, mixed>>
     */
    public function pages(array $params = []): array
    {
        return $this->getList('stats/pages', $params);
    }

    /**
     * @param  array<string, mixed>  $params
     * @return list<array<string, mixed>>
     */
    public function referrers(array $params = []): array
    {
        return $this->getList('stats/referrers', $params);
    }

    /**
     * @param  array<string, mixed>  $params
     * @return list<array<string, mixed>>
     */
    public function sources(array $params = []): array
    {
        return $this->getList('stats/sources', $params);
    }

    /**
     * @param  array<string, mixed>  $params
     * @return list<array<string, mixed>>
     */
    public function browsers(array $params = []): array
    {
        return $this->getList('stats/browsers', $params);
    }

    /**
     * @param  array<string, mixed>  $params
     * @return list<array<string, mixed>>
     */
    public function operatingSystems(array $params = []): array
    {
        return $this->getList('stats/operating-systems', $params);
    }

    /**
     * @param  array<string, mixed>  $params
     * @return list<array<string, mixed>>
     */
    public function devices(array $params = []): array
    {
        return $this->getList('stats/devices', $params);
    }

    /**
     * @param  array<string, mixed>  $params
     * @return list<array<string, mixed>>
     */
    public function languages(array $params = []): array
    {
        return $this->getList('stats/languages', $params);
    }

    /**
     * @param  array<string, mixed>  $params
     * @return list<array<string, mixed>>
     */
    public function campaigns(array $params = []): array
    {
        return $this->getList('stats/campaigns', $params);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function sites(): array
    {
        return $this->getList('sites', [], ttl: 600);
    }

    /*
    |--------------------------------------------------------------------------
    | E-commerce endpoints
    |--------------------------------------------------------------------------
    */

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function ecommerceOverview(array $params = []): array
    {
        return $this->getObject('stats/ecommerce/overview', $params, [
            'revenue' => 0,
            'transactions' => 0,
            'average_order_value' => 0,
            'conversion_rate' => 0,
            'revenue_change' => 0,
            'transactions_change' => 0,
        ]);
    }

    /**
     * @param  array<string, mixed>  $params
     * @return list<array{date: string, revenue: float, transactions: int}>
     */
    public function ecommerceRevenueTimeseries(array $params = []): array
    {
        return $this->getList('stats/ecommerce/revenue-timeseries', $params);
    }

    /**
     * @param  array<string, mixed>  $params
     * @return list<array<string, mixed>>
     */
    public function ecommerceProducts(array $params = []): array
    {
        return $this->getList('stats/ecommerce/products', $params);
    }

    /**
     * @param  array<string, mixed>  $params
     * @return list<array<string, mixed>>
     */
    public function ecommerceFunnel(array $params = []): array
    {
        return $this->getList('stats/ecommerce/funnel', $params);
    }

    /**
     * @param  array<string, mixed>  $params
     * @return list<array<string, mixed>>
     */
    public function ecommerceRevenueBySource(array $params = []): array
    {
        return $this->getList('stats/ecommerce/revenue-by-source', $params);
    }

    /*
    |--------------------------------------------------------------------------
    | Goals endpoints
    |--------------------------------------------------------------------------
    */

    /**
     * @return list<array<string, mixed>>
     */
    public function goals(): array
    {
        return $this->getList('goals', [], ttl: 600);
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array{data: list<array<string, mixed>>, meta: array<string, mixed>}
     */
    public function goalCompletions(string $goalId, array $params = []): array
    {
        /** @var array{data?: mixed, meta?: mixed} $response */
        $response = $this->request('goals/'.$goalId.'/completions', $params, default: ['data' => [], 'meta' => []]);

        return [
            'data' => is_array($response['data'] ?? null) ? array_values($response['data']) : [],
            'meta' => is_array($response['meta'] ?? null) ? $response['meta'] : [],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Internals
    |--------------------------------------------------------------------------
    */

    /**
     * Fetch an endpoint that returns an object under `data`.
     *
     * @param  array<string, mixed>  $params
     * @param  array<string, mixed>  $default
     * @return array<string, mixed>
     */
    protected function getObject(string $endpoint, array $params, array $default, ?int $ttl = null): array
    {
        $data = $this->request($endpoint, $params, default: ['data' => $default], ttl: $ttl)['data'] ?? $default;

        return is_array($data) ? array_merge($default, $data) : $default;
    }

    /**
     * Fetch an endpoint that returns a list under `data`.
     *
     * @param  array<string, mixed>  $params
     * @return list<array<string, mixed>>
     */
    protected function getList(string $endpoint, array $params, ?int $ttl = null): array
    {
        $data = $this->request($endpoint, $params, default: ['data' => []], ttl: $ttl)['data'] ?? [];

        return is_array($data) ? array_values($data) : [];
    }

    /**
     * Perform a cached GET request and return the decoded JSON body.
     *
     * @param  array<string, mixed>  $params
     * @param  array<string, mixed>  $default
     * @return array<string, mixed>
     */
    protected function request(string $endpoint, array $params, array $default, ?int $ttl = null): array
    {
        if (! $this->isConfigured()) {
            return $default;
        }

        $query = $this->normalizeParams($params);
        $ttl ??= (int) config('clear-analytics.cache_ttl', 300);
        $cacheKey = $this->cacheKey($endpoint, $query);

        return Cache::remember($cacheKey, $ttl, function () use ($endpoint, $query, $default): array {
            try {
                $response = $this->client()->get($endpoint, $query);

                if ($response->failed()) {
                    Log::warning('ClearAnalytics API request failed', [
                        'endpoint' => $endpoint,
                        'status' => $response->status(),
                    ]);

                    return $default;
                }

                /** @var array<string, mixed> $json */
                $json = $response->json() ?? $default;

                return $json;
            } catch (Throwable $e) {
                Log::warning('ClearAnalytics API request errored', [
                    'endpoint' => $endpoint,
                    'message' => $e->getMessage(),
                ]);

                return $default;
            }
        });
    }

    protected function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl())
            ->withToken((string) $this->token())
            ->acceptJson()
            ->timeout((int) config('clear-analytics.timeout', 10));
    }

    /**
     * Keep only known params, drop empty values.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    protected function normalizeParams(array $params): array
    {
        return collect($params)
            ->only($this->allowedParams)
            ->reject(fn (mixed $value): bool => $value === null || $value === '')
            ->all();
    }

    /**
     * @param  array<string, mixed>  $query
     */
    protected function cacheKey(string $endpoint, array $query): string
    {
        ksort($query);

        return 'clear-analytics:'.md5($this->baseUrl().'|'.$endpoint.'|'.json_encode($query));
    }
}
