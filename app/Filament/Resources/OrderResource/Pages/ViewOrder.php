<?php
namespace App\Filament\Resources\OrderResource\Pages;

use App\Models\Order;
use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('addPayment')->label('Add Payment')->url(fn () => \App\Filament\Resources\PaymentResource::getUrl('create', ['order_id' => $this->record->id])),
            Actions\EditAction::make(),
            Actions\Action::make('backToPharmacy')->label('Back to Pharmacy')->url(fn () => \App\Filament\Resources\PharmacyResource::getUrl('view', ['record'=>$this->record->pharmacy_id])),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Invoice Summary')->schema([
                TextEntry::make('id')->label('Invoice #'), TextEntry::make('pharmacy.pharmacy_name'), TextEntry::make('invoice_date')->date(), TextEntry::make('status')->badge(),
                TextEntry::make('total_price')->money('USD'), TextEntry::make('paid_amount')->money('USD'), TextEntry::make('remaining_amount')->money('USD'), TextEntry::make('closed_at')->dateTime(),
                TextEntry::make('commission_rate')->formatStateUsing(fn ($state): string => $state ? "{$state}%" : '-'), TextEntry::make('commission_amount')->money('USD'),
                TextEntry::make('commission_explain')->state(fn (?Order $record): string => $record?->isFullyPaid()
                    ? 'Paid: 5% if closed within 2 months, otherwise 3%.'
                    : 'Unpaid/partial: eligible for 5% if fully paid before invoice date + 2 months.'),
                TextEntry::make('notes')->placeholder('-')->columnSpanFull(),
                TextEntry::make('deal_notes')->placeholder('-')->columnSpanFull(),
                TextEntry::make('internal_notes')->placeholder('-')->columnSpanFull(),
            ])->columns(2),
            Section::make('Invoice Items')->schema([RepeatableEntry::make('orderItems')->schema([
                TextEntry::make('product.name'),
                TextEntry::make('quantity'),
                TextEntry::make('bonus_quantity'),
                TextEntry::make('price_at_time')->money('USD'),
                TextEntry::make('line_total')->money('USD'),
                TextEntry::make('bonus_notes')->placeholder('-')->columnSpanFull(),
            ])->columns(5)]),
            Section::make('Payment History')->schema([RepeatableEntry::make('payments')->schema([
                TextEntry::make('payment_date')->date(),
                TextEntry::make('amount')->money('USD'),
                TextEntry::make('payment_method'),
                \Filament\Infolists\Components\IconEntry::make('is_cash_bonus')->boolean()->label('Cash Bonus'),
                \Filament\Infolists\Components\IconEntry::make('is_single_transaction_bonus')->boolean()->label('Single Tx Bonus'),
                TextEntry::make('bonus_notes')->placeholder('-'),
                TextEntry::make('notes')->placeholder('-'),
            ])->columns(4)]),
        ]);
    }
}
