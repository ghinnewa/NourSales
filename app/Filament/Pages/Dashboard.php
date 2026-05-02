<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BusinessOverviewStats;
use App\Filament\Widgets\InvoiceStatusChart;
use App\Filament\Widgets\MonthlyCommissionChart;
use App\Filament\Widgets\MonthlySalesPaymentsChart;
use App\Filament\Widgets\OutstandingBalanceByPharmacyChart;
use App\Filament\Widgets\OutstandingInvoicesTable;
use App\Filament\Widgets\PaymentMethodsChart;
use App\Filament\Widgets\RecentPharmacies;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'لوحة التحكم';

    public function getWidgets(): array
    {
        return [
            BusinessOverviewStats::class,
            MonthlySalesPaymentsChart::class,
            MonthlyCommissionChart::class,
            InvoiceStatusChart::class,
            PaymentMethodsChart::class,
            OutstandingBalanceByPharmacyChart::class,
            RecentPharmacies::class,
            OutstandingInvoicesTable::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 2;
    }
}
