<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Order;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Sales';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('order_id')->label('Invoice')->required()->searchable()->live()
                ->options(fn () => Order::query()->with('pharmacy')->get()->mapWithKeys(fn ($o) => [$o->id => "Invoice #{$o->id} | {$o->pharmacy->pharmacy_name} | {$o->invoice_date->toDateString()} | Total ".number_format((float)$o->total_price,2)." | Remaining ".number_format($o->remaining_amount,2)])->toArray())
                ->afterStateUpdated(function ($state, Forms\Set $set) {
                    if ($order = Order::find($state)) {
                        $set('pharmacy_id', $order->pharmacy_id);
                        $set('amount', $order->remaining_amount);
                    }
                }),
            Forms\Components\Select::make('pharmacy_id')->relationship('pharmacy', 'pharmacy_name')->required()->disabled()->dehydrated(),
            Forms\Components\TextInput::make('amount')->required()->numeric()->minValue(0.01),
            Forms\Components\DatePicker::make('payment_date')->required()->default(now()->toDateString()),
            Forms\Components\Select::make('payment_method')->options(['cash'=>'Cash','bank_transfer'=>'Bank Transfer','cheque'=>'Cheque','other'=>'Other']),
            Forms\Components\Textarea::make('notes')->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('order_id')->label('Invoice #'),
            Tables\Columns\TextColumn::make('pharmacy.pharmacy_name')->searchable(),
            Tables\Columns\TextColumn::make('amount')->money('USD'),
            Tables\Columns\TextColumn::make('payment_date')->date(),
            Tables\Columns\TextColumn::make('payment_method'),
            Tables\Columns\TextColumn::make('created_at')->dateTime(),
        ])->filters([
            SelectFilter::make('payment_method')->options(['cash'=>'Cash','bank_transfer'=>'Bank Transfer','cheque'=>'Cheque','other'=>'Other']),
            SelectFilter::make('pharmacy_id')->relationship('pharmacy', 'pharmacy_name')->label('Pharmacy'),
            Filter::make('payment_date_range')->form([
                Forms\Components\DatePicker::make('from'), Forms\Components\DatePicker::make('until'),
            ])->query(fn ($query, array $data) => $query
                ->when($data['from'] ?? null, fn($q,$d)=>$q->whereDate('payment_date','>=',$d))
                ->when($data['until'] ?? null, fn($q,$d)=>$q->whereDate('payment_date','<=',$d))),
        ])->actions([Tables\Actions\ViewAction::make(), Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index'=>Pages\ListPayments::route('/'),'create'=>Pages\CreatePayment::route('/create'),'view'=>Pages\ViewPayment::route('/{record}'),'edit'=>Pages\EditPayment::route('/{record}/edit')];
    }
}
