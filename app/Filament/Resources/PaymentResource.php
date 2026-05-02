<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Order;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Sales';
    protected static ?string $navigationLabel = 'Payments';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Payment Details')->schema([
                Forms\Components\Select::make('pharmacy_id')->relationship('pharmacy', 'pharmacy_name')->required()->searchable()->live(),
                Forms\Components\Select::make('order_id')->label('Order / Invoice')->searchable()->options(function (Forms\Get $get) {
                    $pharmacyId = $get('pharmacy_id'); if (! $pharmacyId) return [];
                    return Order::query()->where('pharmacy_id', $pharmacyId)->whereIn('status', ['pending', 'delivered'])->get()->mapWithKeys(fn ($o) => [$o->id => "#{$o->id} | {$o->invoice_date->toDateString()} | $".number_format((float)$o->total_price,2)." | Remaining $".number_format($o->remainingAmount(),2)])->toArray();
                }),
                Forms\Components\TextInput::make('amount')->required()->numeric()->minValue(0.01),
                Forms\Components\DatePicker::make('payment_date')->required()->default(now()->toDateString()),
                Forms\Components\Select::make('payment_method')->options(['cash'=>'Cash','bank_transfer'=>'Bank Transfer','cheque'=>'Cheque','other'=>'Other']),
                Forms\Components\Textarea::make('notes')->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('pharmacy.pharmacy_name')->searchable(),
            Tables\Columns\TextColumn::make('order_id')->label('Order #'),
            Tables\Columns\TextColumn::make('amount')->money('USD')->sortable(),
            Tables\Columns\TextColumn::make('payment_date')->date()->sortable(),
            Tables\Columns\TextColumn::make('payment_method'),
            Tables\Columns\TextColumn::make('created_at')->dateTime(),
        ])->filters([
            SelectFilter::make('payment_method')->options(['cash'=>'Cash','bank_transfer'=>'Bank Transfer','cheque'=>'Cheque','other'=>'Other']),
        ])->actions([
            Tables\Actions\ViewAction::make(),Tables\Actions\EditAction::make(),Tables\Actions\DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'view' => Pages\ViewPayment::route('/{record}'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
