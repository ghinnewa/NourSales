<?php
namespace App\Filament\Widgets;

use App\Models\Pharmacy;
use Filament\Widgets\ChartWidget;

class OutstandingBalanceByPharmacyChart extends ChartWidget
{
    protected static ?string $heading = 'Top Outstanding Balance by Pharmacy';
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = Pharmacy::query()->get()->map(function (Pharmacy $pharmacy) {
            return [
                'name' => $pharmacy->pharmacy_name,
                'balance' => max(0, $pharmacy->currentBalance()),
            ];
        })->where('balance', '>', 0)->sortByDesc('balance')->take(5)->values();

        return [
            'datasets' => [[
                'label' => 'Outstanding Balance',
                'data' => $data->pluck('balance')->all(),
                'backgroundColor' => '#F97316',
            ]],
            'labels' => $data->pluck('name')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
