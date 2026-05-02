<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\PharmaciesOverview;
use App\Filament\Widgets\SalesStatsWidget;
use App\Filament\Widgets\CommissionStatsWidget;
use App\Filament\Widgets\RecentPharmacies;
use App\Filament\Widgets\DebtStatsWidget;
use App\Filament\Widgets\FinancialStatsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Sales Rep Companion';

    public function getWidgets(): array
    {
        return [
            PharmaciesOverview::class,
            RecentPharmacies::class,
            SalesStatsWidget::class,
            CommissionStatsWidget::class,
            FinancialStatsWidget::class,
            DebtStatsWidget::class,
        ];
    }
}
