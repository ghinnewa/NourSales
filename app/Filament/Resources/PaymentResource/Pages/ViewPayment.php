<?php
namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Payment')->schema([
                TextEntry::make('pharmacy.pharmacy_name')->label('Pharmacy'),
                TextEntry::make('order_id')->label('Invoice #'),
                TextEntry::make('amount')->money('USD'),
                TextEntry::make('payment_date')->date(),
                TextEntry::make('payment_method'),
                \Filament\Infolists\Components\IconEntry::make('is_cash_bonus')->boolean()->label('Cash Bonus'),
                \Filament\Infolists\Components\IconEntry::make('is_single_transaction_bonus')->boolean()->label('Single Transaction Bonus'),
                TextEntry::make('bonus_notes')->placeholder('-'),
                TextEntry::make('notes'),
                TextEntry::make('created_at')->dateTime(),
            ])->columns(2),
        ]);
    }
}
