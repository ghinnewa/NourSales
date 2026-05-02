<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CommissionStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $openOrders = Order::whereIn('status', ['pending', 'delivered'])->get();
        $pendingEstimate = $openOrders->sum(function ($order) {
            $rate = now()->lte($order->invoice_date->copy()->addMonths(2)->endOfDay()) ? 5 : 3;
            return ((float)$order->total_price * $rate) / 100;
        });

        return [
            Stat::make('Invoices Closed Within 2 Months', Order::where('status', 'closed')->where('commission_rate', 5)->count()),
            Stat::make('Invoices Closed After 2 Months', Order::where('status', 'closed')->where('commission_rate', 3)->count()),
            Stat::make('Open Orders / Invoices', $openOrders->count()),
            Stat::make('Potential Commission Pending', '$'.number_format((float)$pendingEstimate, 2)),
        ];
    }
}
