<?php
namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class MonthlyCommissionChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Commission (Closed Invoices)';
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $months = collect(range(5, 0))->map(fn (int $i) => now()->startOfMonth()->subMonths($i));
        $months = $months->push(now()->startOfMonth());

        $orders = Order::where('status', 'closed')
            ->whereNotNull('closed_at')
            ->whereDate('closed_at', '>=', now()->startOfMonth()->subMonths(5))
            ->get()
            ->groupBy(fn (Order $o) => optional($o->closed_at)->format('Y-m'));

        $rate5 = $months->map(function (Carbon $month) use ($orders) {
            return (float) ($orders->get($month->format('Y-m'), collect())->where('commission_rate', '5.00')->sum('commission_amount'));
        })->all();

        $rate3 = $months->map(function (Carbon $month) use ($orders) {
            return (float) ($orders->get($month->format('Y-m'), collect())->where('commission_rate', '3.00')->sum('commission_amount'));
        })->all();

        return [
            'datasets' => [
                ['label' => '5% Commission', 'data' => $rate5, 'backgroundColor' => '#0D9488'],
                ['label' => '3% Commission', 'data' => $rate3, 'backgroundColor' => '#F59E0B'],
            ],
            'labels' => $months->map(fn (Carbon $month) => $month->format('M Y'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
