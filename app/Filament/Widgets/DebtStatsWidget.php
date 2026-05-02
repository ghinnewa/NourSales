<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Pharmacy;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DebtStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $withDebt = Pharmacy::all()->filter(fn ($p) => $p->currentBalance() > 0)->count();
        $highest = Pharmacy::all()->sortByDesc(fn ($p) => $p->currentBalance())->take(1)->first();
        return [
            Stat::make('Pharmacies With Outstanding Balance', $withDebt),
            Stat::make('Highest Balance Pharmacy', $highest ? $highest->pharmacy_name.' ($'.number_format($highest->currentBalance(), 2).')' : 'N/A'),
            Stat::make('Open Orders / Invoices', Order::whereIn('status', ['pending', 'delivered'])->count()),
            Stat::make('Orders Still Eligible for 5% Commission', Order::whereIn('status', ['pending','delivered'])->get()->filter(fn($o)=> now()->lte($o->invoice_date->copy()->addMonths(2)->endOfDay()))->count()),
        ];
    }
}
