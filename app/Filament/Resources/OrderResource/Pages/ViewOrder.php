<?php
namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Order')->schema([
                TextEntry::make('id'), TextEntry::make('pharmacy.pharmacy_name')->label('Pharmacy'), TextEntry::make('invoice_date')->date(), TextEntry::make('status'), TextEntry::make('total_price')->money('USD'), TextEntry::make('paid_amount')->state(fn ($record) => $record->paidAmount())->money('USD'), TextEntry::make('remaining_amount')->state(fn ($record) => $record->remainingAmount())->money('USD'), TextEntry::make('closed_at')->dateTime(), TextEntry::make('commission_rate')->formatStateUsing(fn ($s) => $s ? $s.'%' : '-'), TextEntry::make('commission_amount')->money('USD'), TextEntry::make('notes'), TextEntry::make('created_at')->dateTime(),
            ])->columns(2),
            Section::make('Order Items')->schema([
                RepeatableEntry::make('orderItems')->schema([
                    TextEntry::make('product.name')->label('Product'), TextEntry::make('quantity'), TextEntry::make('price_at_time')->money('USD'), TextEntry::make('line_total')->money('USD'),
                ])->columns(4),
            ]),
            Section::make('Payment History')->schema([
                RepeatableEntry::make('payments')->schema([
                    TextEntry::make('amount')->money('USD'),
                    TextEntry::make('payment_date')->date(),
                    TextEntry::make('payment_method'),
                ])->columns(3),
            ]),
            Section::make('Commission Explanation')->schema([
                TextEntry::make('commission_message')->state(function ($record) {
                    if ($record->status !== 'closed' || ! $record->closed_at) return 'Commission will be calculated when the invoice is closed.';
                    return ((float)$record->commission_rate === 5.0) ? 'Closed within 2 months — 5% commission applied.' : 'Closed after 2 months — 3% commission applied.';
                }),
            ]),
        ]);
    }
}
