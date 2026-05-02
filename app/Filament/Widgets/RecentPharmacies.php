<?php

namespace App\Filament\Widgets;

use App\Models\Pharmacy;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentPharmacies extends BaseWidget
{
    protected static ?string $heading = 'Recent Pharmacies';

    public function table(Table $table): Table
    {
        return $table
            ->query(Pharmacy::query()->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('pharmacy_name')->label('Pharmacy Name'),
                Tables\Columns\TextColumn::make('owner_name')->label('Owner Name'),
                Tables\Columns\TextColumn::make('area'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ]);
    }
}
