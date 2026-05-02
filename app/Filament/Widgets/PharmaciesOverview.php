<?php

namespace App\Filament\Widgets;

use App\Models\Pharmacy;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PharmaciesOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Pharmacies', Pharmacy::count()),
            Stat::make('Total Products', Product::count()),
            Stat::make('Areas Covered', Pharmacy::whereNotNull('area')->distinct('area')->count('area')),
            Stat::make('Pharmacies Added This Month', Pharmacy::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count()),
        ];
    }
}
