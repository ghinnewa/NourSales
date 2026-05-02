<?php

namespace App\Filament\Widgets;

use App\Models\Pharmacy;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentPharmacies extends BaseWidget
{
    protected static ?string $heading = 'أحدث الصيدليات';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Pharmacy::query()->latest()->limit(10))
            ->columns([
                Tables\Columns\TextColumn::make('pharmacy_name')->label('Pharmacy Name')->searchable(),
                Tables\Columns\TextColumn::make('owner_name')->label('Owner Name'),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('area'),
                Tables\Columns\TextColumn::make('current_balance')->state(fn (Pharmacy $record) => $record->currentBalance())->money('USD')->label('Current Balance'),
                Tables\Columns\TextColumn::make('latest_invoice_date')
                    ->label('Latest Invoice Date')
                    ->state(fn (Pharmacy $record) => optional($record->orders()->latest('invoice_date')->first())->invoice_date)
                    ->date(),
            ])
            ->emptyStateHeading('No pharmacies yet.');
    }
}
