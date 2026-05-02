<?php
namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class InvoiceStatusChart extends ChartWidget
{
    protected static ?string $heading = 'حالات الفواتير';

    protected function getData(): array
    {
        $statuses = ['pending', 'delivered', 'closed', 'cancelled'];
        $counts = collect($statuses)->map(fn (string $status) => Order::where('status', $status)->count())->all();

        return [
            'datasets' => [[
                'data' => $counts,
                'backgroundColor' => ['#3B82F6', '#F97316', '#16A34A', '#DC2626'],
            ]],
            'labels' => ['قيد الانتظار','تم التسليم','مغلقة','ملغاة'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
