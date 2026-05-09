<?php

namespace App\Filament\Resources\PharmacyResource\RelationManagers;

use App\Filament\Resources\OrderResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $title = 'Orders / Invoices';

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id'),
            Tables\Columns\TextColumn::make('invoice_date')->date()->sortable(),
            Tables\Columns\TextColumn::make('status')->badge(),
            Tables\Columns\TextColumn::make('total_price')->money('USD', locale: 'en'),
            Tables\Columns\TextColumn::make('commission_amount')->money('USD', locale: 'en'),
        ])->headerActions([
            Tables\Actions\Action::make('createOrder')
                ->label('Create Order / Invoice')
                ->icon('heroicon-o-plus')
                ->url(fn () => OrderResource::getUrl('create', ['pharmacy_id' => $this->ownerRecord->id])),
        ])->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
        ]);
    }
}
