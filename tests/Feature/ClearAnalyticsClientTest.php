<?php

use ClearAnalytics\Filament\ClearAnalytics;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->client = app(ClearAnalytics::class);
});

it('reports configured state from the token', function () {
    expect($this->client->isConfigured())->toBeTrue();

    config(['clear-analytics.token' => null]);
    expect($this->client->isConfigured())->toBeFalse();
});

it('does not call the API when no token is configured', function () {
    config(['clear-analytics.token' => null]);
    Http::fake();

    expect($this->client->overview())->toBeArray();

    Http::assertNothingSent();
});

it('sends the bearer token and query params, and unwraps the data envelope', function () {
    Http::fake([
        '*/stats/overview*' => Http::response(['data' => [
            'visitors' => 1523,
            'pageviews' => 4891,
            'bounce_rate' => 42.3,
            'avg_session_duration' => 187,
            'visitors_change' => 12.5,
            'pageviews_change' => 8.2,
            'bounce_rate_change' => -3.1,
            'avg_session_duration_change' => 15.7,
        ]]),
    ]);

    $overview = $this->client->overview(['period' => '30d', 'site_id' => '01JMABC123']);

    expect($overview['visitors'])->toBe(1523)
        ->and($overview['pageviews'])->toBe(4891)
        ->and($overview['avg_session_duration'])->toBe(187);

    Http::assertSent(function ($request) {
        return str_contains($request->url(), '/stats/overview')
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request['period'] === '30d'
            && $request['site_id'] === '01JMABC123';
    });
});

it('drops empty and unknown params', function () {
    Http::fake(['*' => Http::response(['data' => []])]);

    $this->client->pages(['period' => '7d', 'site_id' => null, 'bogus' => 'x', 'limit' => 10]);

    Http::assertSent(function ($request) {
        return ! str_contains($request->url(), 'site_id')
            && ! str_contains($request->url(), 'bogus')
            && str_contains($request->url(), 'limit=10');
    });
});

it('unwraps list endpoints into a plain array', function () {
    Http::fake([
        '*/stats/pages*' => Http::response(['data' => [
            ['name' => '/', 'visitors' => 890, 'pageviews' => 1234],
            ['name' => '/pricing', 'visitors' => 120, 'pageviews' => 240],
        ]]),
    ]);

    $pages = $this->client->pages();

    expect($pages)->toHaveCount(2)
        ->and($pages[0]['name'])->toBe('/')
        ->and($pages[1]['visitors'])->toBe(120);
});

it('caches responses so repeated calls hit the API once', function () {
    Http::fake(['*/stats/overview*' => Http::response(['data' => ['visitors' => 5]])]);

    $this->client->overview(['period' => '7d']);
    $this->client->overview(['period' => '7d']);

    Http::assertSentCount(1);
});

it('returns safe defaults and does not throw on API failure', function () {
    Http::fake(['*/stats/overview*' => Http::response(['error' => 'Unauthorized'], 401)]);

    $overview = $this->client->overview();

    expect($overview['visitors'])->toBe(0)
        ->and($overview['bounce_rate'])->toBe(0);
});

it('returns an empty list on failure for breakdown endpoints', function () {
    Http::fake(['*/stats/referrers*' => Http::response('', 429)]);

    expect($this->client->referrers())->toBe([]);
});
