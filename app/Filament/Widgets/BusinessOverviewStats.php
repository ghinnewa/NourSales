<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Pharmacy;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BusinessOverviewStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalSales = (float) Order::where('status', '!=', 'cancelled')->sum('total_price');
        $paymentsCollected = (float) Payment::sum('amount');
        $outstandingBalance = max(0, $totalSales - $paymentsCollected);
        $commissionEarned = (float) Order::where('status', 'closed')->sum('commission_amount');

        return [
            Stat::make('إجمالي الصيدليات', (string) Pharmacy::count())
                ->description('Active client accounts')
                ->color('info')
                ->icon('heroicon-o-building-storefront'),
            Stat::make('إجمالي المنتجات', (string) Product::count())
                ->description('Products in catalog')
                ->color('gray')
                ->icon('heroicon-o-cube'),
            Stat::make('إجمالي الفواتير', (string) Order::count())
                ->description('All invoice records')
                ->color('warning')
                ->icon('heroicon-o-document-text'),
            Stat::make('إجمالي المبيعات', '$' . number_format($totalSales, 2))
                ->description('Non-cancelled invoices')
                ->color('success')
                ->icon('heroicon-o-banknotes'),
            Stat::make('الدفعات المحصلة', '$' . number_format($paymentsCollected, 2))
                ->description('Captured from payment records')
                ->color('success')
                ->icon('heroicon-o-credit-card'),
            Stat::make('الرصيد المستحق', '$' . number_format($outstandingBalance, 2))
                ->description('Sales value minus payments')
                ->color($outstandingBalance > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-exclamation-circle'),
            Stat::make('العمولة المكتسبة', '$' . number_format($commissionEarned, 2))
                ->description('Closed invoices only')
                ->color('teal')
                ->icon('heroicon-o-chart-bar'),
        ];
    }
}
