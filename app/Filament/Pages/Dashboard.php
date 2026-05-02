<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\PharmaciesOverview;
use App\Filament\Widgets\RecentPharmacies;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Sales Rep Companion';

    public function getWidgets(): array
    {
        return [
            PharmaciesOverview::class,
            RecentPharmacies::class,
        ];
    }
}
