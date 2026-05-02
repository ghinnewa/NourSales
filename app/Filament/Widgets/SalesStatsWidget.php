<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Orders', Order::count()),
            Stat::make('Orders This Month', Order::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count()),
            Stat::make('Total Sales Value', '$'.number_format((float) Order::sum('total_price'), 2)),
            Stat::make('Total Commission Earned', '$'.number_format((float) Order::sum('commission_amount'), 2)),
        ];
    }
}
