<?php

namespace App\Filament\Resources\PharmacyResource\RelationManagers;

use App\Filament\Resources\PaymentResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';
    protected static ?string $title = 'Payments History';

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('order_id')->label('Order #'),
            Tables\Columns\TextColumn::make('amount')->money('USD'),
            Tables\Columns\TextColumn::make('payment_date')->date()->sortable(),
            Tables\Columns\TextColumn::make('payment_method'),
        ])->headerActions([
            Tables\Actions\Action::make('addPayment')
                ->label('Add Payment')
                ->icon('heroicon-o-plus')
                ->url(fn () => PaymentResource::getUrl('create', ['pharmacy_id' => $this->ownerRecord->id])),
        ]);
    }
}
