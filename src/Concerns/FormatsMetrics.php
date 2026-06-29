<?php

namespace ClearAnalytics\Filament\Concerns;

use Filament\Support\Enums\IconPosition;

/**
 * Formatting helpers shared by the stat widgets: human numbers, durations,
 * percentages, and the percentage-change trend indicator (label/icon/colour).
 */
trait FormatsMetrics
{
    protected function formatNumber(int|float $value): string
    {
        $value = (float) $value;

        if (abs($value) >= 1_000_000) {
            return rtrim(rtrim(number_format($value / 1_000_000, 1), '0'), '.').'M';
        }

        if (abs($value) >= 1_000) {
            return rtrim(rtrim(number_format($value / 1_000, 1), '0'), '.').'K';
        }

        return number_format($value);
    }

    protected function formatDuration(int|float $seconds): string
    {
        $seconds = (int) round((float) $seconds);

        if ($seconds <= 0) {
            return '0s';
        }

        $minutes = intdiv($seconds, 60);
        $remaining = $seconds % 60;

        if ($minutes === 0) {
            return $remaining.'s';
        }

        return $minutes.'m '.$remaining.'s';
    }

    protected function formatPercent(int|float $value): string
    {
        return number_format((float) $value, 1).'%';
    }

    protected function formatMoney(int|float $value): string
    {
        return number_format((float) $value, 2);
    }

    /**
     * Human-readable change label, e.g. "+12.5% vs previous period".
     */
    protected function changeDescription(int|float $change): string
    {
        $change = (float) $change;
        $sign = $change > 0 ? '+' : '';

        return $sign.number_format($change, 1).'% '.__('clear-analytics::clear-analytics.vs_previous');
    }

    protected function changeIcon(int|float $change): string
    {
        $change = (float) $change;

        return match (true) {
            $change > 0 => 'heroicon-m-arrow-trending-up',
            $change < 0 => 'heroicon-m-arrow-trending-down',
            default => 'heroicon-m-minus',
        };
    }

    /**
     * Colour for the change indicator. When $higherIsBetter is false (e.g. bounce
     * rate), the up/down colours are swapped so a rise reads as negative.
     */
    protected function changeColor(int|float $change, bool $higherIsBetter = true): string
    {
        $change = (float) $change;

        if ($change === 0.0) {
            return (string) config('clear-analytics.trends.steady', 'gray');
        }

        $isPositive = $higherIsBetter ? $change > 0 : $change < 0;

        return (string) config('clear-analytics.trends.'.($isPositive ? 'up' : 'down'), $isPositive ? 'success' : 'danger');
    }

    protected function changeIconPosition(): IconPosition
    {
        return IconPosition::Before;
    }
}
