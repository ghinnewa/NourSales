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
            Stat::make('Total Pharmacies', (string) Pharmacy::count())
                ->description('Active client accounts')
                ->color('info')
                ->icon('heroicon-o-building-storefront'),
            Stat::make('Total Products', (string) Product::count())
                ->description('Products in catalog')
                ->color('gray')
                ->icon('heroicon-o-cube'),
            Stat::make('Total Invoices', (string) Order::count())
                ->description('All invoice records')
                ->color('warning')
                ->icon('heroicon-o-document-text'),
            Stat::make('Total Sales Value', '$' . number_format($totalSales, 2))
                ->description('Non-cancelled invoices')
                ->color('success')
                ->icon('heroicon-o-banknotes'),
            Stat::make('Payments Collected', '$' . number_format($paymentsCollected, 2))
                ->description('Captured from payment records')
                ->color('success')
                ->icon('heroicon-o-credit-card'),
            Stat::make('Outstanding Balance', '$' . number_format($outstandingBalance, 2))
                ->description('Sales value minus payments')
                ->color($outstandingBalance > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-exclamation-circle'),
            Stat::make('Commission Earned', '$' . number_format($commissionEarned, 2))
                ->description('Closed invoices only')
                ->color('teal')
                ->icon('heroicon-o-chart-bar'),
        ];
    }
}
