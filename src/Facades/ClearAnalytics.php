<?php

namespace ClearAnalytics\Filament\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string baseUrl()
 * @method static string|null token()
 * @method static bool isConfigured()
 * @method static array<string, mixed> overview(array<string, mixed> $params = [])
 * @method static list<array{date: string, visitors: int, pageviews: int}> timeseries(array<string, mixed> $params = [])
 * @method static array<string, mixed> live(array<string, mixed> $params = [])
 * @method static list<array<string, mixed>> pages(array<string, mixed> $params = [])
 * @method static list<array<string, mixed>> referrers(array<string, mixed> $params = [])
 * @method static list<array<string, mixed>> sources(array<string, mixed> $params = [])
 * @method static list<array<string, mixed>> browsers(array<string, mixed> $params = [])
 * @method static list<array<string, mixed>> operatingSystems(array<string, mixed> $params = [])
 * @method static list<array<string, mixed>> devices(array<string, mixed> $params = [])
 * @method static list<array<string, mixed>> languages(array<string, mixed> $params = [])
 * @method static list<array<string, mixed>> campaigns(array<string, mixed> $params = [])
 * @method static list<array<string, mixed>> sites()
 * @method static array<string, mixed> ecommerceOverview(array<string, mixed> $params = [])
 * @method static list<array{date: string, revenue: float, transactions: int}> ecommerceRevenueTimeseries(array<string, mixed> $params = [])
 * @method static list<array<string, mixed>> ecommerceProducts(array<string, mixed> $params = [])
 * @method static list<array<string, mixed>> ecommerceFunnel(array<string, mixed> $params = [])
 * @method static list<array<string, mixed>> ecommerceRevenueBySource(array<string, mixed> $params = [])
 * @method static list<array<string, mixed>> goals()
 * @method static array{data: list<array<string, mixed>>, meta: array<string, mixed>} goalCompletions(string $goalId, array<string, mixed> $params = [])
 *
 * @see \ClearAnalytics\Filament\ClearAnalytics
 */
class ClearAnalytics extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \ClearAnalytics\Filament\ClearAnalytics::class;
    }
}
