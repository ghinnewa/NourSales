<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinancialStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $sales = (float) Order::sum('total_price');
        $payments = (float) Payment::sum('amount');
        return [
            Stat::make('Total Sales Value', '$'.number_format($sales, 2)),
            Stat::make('Total Payments Collected', '$'.number_format($payments, 2)),
            Stat::make('Total Outstanding Balance', '$'.number_format($sales - $payments, 2)),
            Stat::make('Total Commission Earned', '$'.number_format((float) Order::sum('commission_amount'), 2)),
        ];
    }
}
