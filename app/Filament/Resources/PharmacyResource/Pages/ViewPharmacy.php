<?php

namespace App\Filament\Resources\PharmacyResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Filament\Resources\PaymentResource;
use App\Filament\Resources\PharmacyResource;
use App\Models\Pharmacy;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewPharmacy extends ViewRecord
{
    protected static string $resource = PharmacyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('createInvoice')->label('Create Invoice for this Pharmacy')->url(fn()=>OrderResource::getUrl('create',['pharmacy_id'=>$this->record->id])),
            Actions\Action::make('addPayment')->label('Add Payment to Existing Invoice')->url(fn()=>PaymentResource::getUrl('create',['pharmacy_id'=>$this->record->id])),
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Pharmacy Details')->schema([
                Infolists\Components\TextEntry::make('pharmacy_name'), Infolists\Components\TextEntry::make('owner_name'), Infolists\Components\TextEntry::make('phone'),
            ])->columns(3),
            Infolists\Components\Section::make('Current Balance')->schema([
                Infolists\Components\TextEntry::make('total_invoices')->state(fn (?Pharmacy $record): string => number_format($record?->totalOrdersValue() ?? 0, 2)),
                Infolists\Components\TextEntry::make('total_payments')->state(fn (?Pharmacy $record): string => number_format($record?->totalPaymentsValue() ?? 0, 2)),
                Infolists\Components\TextEntry::make('outstanding')->state(fn (?Pharmacy $record): string => number_format($record?->currentBalance() ?? 0, 2)),
            ])->columns(3),
        ]);
    }
}
