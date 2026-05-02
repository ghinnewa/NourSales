<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class MonthlySalesPaymentsChart extends ChartWidget
{
    protected static ?string $heading = 'المبيعات والدفعات الشهرية';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $months = collect(range(5, 0))->map(fn (int $i) => now()->startOfMonth()->subMonths($i));
        $months = $months->push(now()->startOfMonth());

        $labels = $months->map(fn (Carbon $month) => $month->format('M Y'))->all();

        $salesByMonth = Order::query()->where('status', '!=', 'cancelled')->whereDate('invoice_date', '>=', now()->startOfMonth()->subMonths(5))
            ->get()->groupBy(fn (Order $o) => optional($o->invoice_date)->format('Y-m'))
            ->map(fn ($group) => (float) $group->sum('total_price'));

        $paymentsByMonth = Payment::query()->whereDate('payment_date', '>=', now()->startOfMonth()->subMonths(5))
            ->get()->groupBy(fn (Payment $p) => optional($p->payment_date)->format('Y-m'))
            ->map(fn ($group) => (float) $group->sum('amount'));

        $salesData = $months->map(fn (Carbon $month) => $salesByMonth->get($month->format('Y-m'), 0))->all();
        $paymentsData = $months->map(fn (Carbon $month) => $paymentsByMonth->get($month->format('Y-m'), 0))->all();

        return [
            'datasets' => [
                ['label' => 'المبيعات', 'data' => $salesData, 'borderColor' => '#2563EB', 'backgroundColor' => 'rgba(37,99,235,0.2)'],
                ['label' => 'الدفعات', 'data' => $paymentsData, 'borderColor' => '#16A34A', 'backgroundColor' => 'rgba(22,163,74,0.2)'],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
