<?php

namespace App\Filament\Resources\PharmacyResource\Pages;

use App\Filament\Resources\PharmacyResource;
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
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Pharmacy Profile')
                ->schema([
                    Infolists\Components\TextEntry::make('pharmacy_name')->label('Pharmacy Name'),
                    Infolists\Components\TextEntry::make('owner_name')->label('Owner Name'),
                    Infolists\Components\TextEntry::make('phone')->label('Phone'),
                    Infolists\Components\TextEntry::make('area')->label('Area'),
                    Infolists\Components\TextEntry::make('address')->label('Address'),
                    Infolists\Components\TextEntry::make('google_maps_link')->label('Google Maps Link')->url(fn ($state) => $state, true),
                    Infolists\Components\TextEntry::make('notes')->label('Notes'),
                ])->columns(2),
            Infolists\Components\Section::make('Financial Summary')->schema([
                Infolists\Components\TextEntry::make('current_balance')->state(fn ($record) => '$'.number_format($record->currentBalance(), 2)),
                Infolists\Components\TextEntry::make('total_orders')->state(fn ($record) => '$'.number_format($record->totalOrdersValue(), 2)),
                Infolists\Components\TextEntry::make('total_payments')->state(fn ($record) => '$'.number_format($record->totalPaymentsValue(), 2)),
            ])->columns(3),
        ]);
    }
}
