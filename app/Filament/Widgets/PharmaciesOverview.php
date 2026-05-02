<?php

namespace App\Filament\Widgets;

use App\Models\Pharmacy;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PharmaciesOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Pharmacies', Pharmacy::count()),
            Stat::make('Pharmacies Added This Month', Pharmacy::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count()),
            Stat::make('Areas Covered', Pharmacy::whereNotNull('area')->distinct('area')->count('area')),
        ];
    }
}
