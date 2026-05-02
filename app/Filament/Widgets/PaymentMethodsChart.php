<?php
namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;

class PaymentMethodsChart extends ChartWidget
{
    protected static ?string $heading = 'طرق الدفع';

    protected function getData(): array
    {
        $methods = ['cash', 'bank_transfer', 'cheque', 'other'];

        return [
            'datasets' => [[
                'data' => collect($methods)->map(fn (string $m) => Payment::where('payment_method', $m)->count())->all(),
                'backgroundColor' => ['#22C55E', '#3B82F6', '#A855F7', '#F97316'],
            ]],
            'labels' => ['نقداً','تحويل مصرفي','صك','أخرى'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
