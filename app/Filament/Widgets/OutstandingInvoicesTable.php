<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OutstandingInvoicesTable extends BaseWidget
{
    protected static ?string $heading = 'الفواتير المستحقة';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->whereIn('status', ['pending', 'delivered'])->with(['pharmacy', 'payments']))
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Invoice #')->sortable(),
                Tables\Columns\TextColumn::make('pharmacy.pharmacy_name')->label('Pharmacy')->searchable(),
                Tables\Columns\TextColumn::make('invoice_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('total_price')->money('USD')->sortable(),
                Tables\Columns\TextColumn::make('paid_amount')->label('Paid Amount')->money('USD')->sortable(),
                Tables\Columns\TextColumn::make('remaining_amount')->label('Remaining')->money('USD')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('commission_amount')->money('USD')->label('Commission'),
                Tables\Columns\TextColumn::make('invoice_date')->label('5% Deadline')->date()->state(fn (Order $record) => optional($record->invoice_date)?->copy()?->addMonths(2)),
            ])
            ->defaultSort('total_price', 'desc')
            ->actions([
                Tables\Actions\Action::make('view')->label('عرض الفاتورة')->url(fn (Order $record): string => OrderResource::getUrl('view', ['record' => $record])),
            ])
            ->emptyStateHeading('No outstanding invoices');
    }
}
