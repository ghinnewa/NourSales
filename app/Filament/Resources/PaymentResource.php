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
    protected static ?string $navigationGroup = 'المبيعات';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('order_id')->label('الفاتورة')->required()->searchable()->live()
                ->options(function (Forms\Get $get): array {
                    $query = Order::query()->with('pharmacy');

                    if ($pharmacyId = $get('pharmacy_id')) {
                        $query->where('pharmacy_id', $pharmacyId);
                    }

                    return $query
                        ->get()
                        ->mapWithKeys(fn ($o) => [$o->id => "Invoice #{$o->id} | {$o->pharmacy->pharmacy_name} | {$o->invoice_date->toDateString()} | Total " . number_format((float) $o->total_price, 2) . " | Remaining " . number_format($o->remaining_amount, 2)])
                        ->toArray();
                })
                ->afterStateUpdated(function ($state, Forms\Set $set) {
                    if ($order = Order::find($state)) {
                        $set('pharmacy_id', $order->pharmacy_id);
                        $set('amount', $order->remaining_amount);
                    }
                }),
            Forms\Components\Select::make('pharmacy_id')->relationship('pharmacy', 'pharmacy_name')->required()->disabled()->dehydrated(),
            Forms\Components\TextInput::make('amount')->required()->numeric()->minValue(0.01),
            Forms\Components\DatePicker::make('payment_date')->required()->default(now()->toDateString()),
            Forms\Components\Select::make('payment_method')
                ->options(['cash' => 'نقداً', 'bank_transfer' => 'تحويل مصرفي', 'cheque' => 'صك', 'other' => 'أخرى'])
                ->required()
                ->live(),
            Forms\Components\Textarea::make('notes')->columnSpanFull(),
            Forms\Components\Section::make('ملاحظات البونص والاتفاق')->schema([
                Forms\Components\Toggle::make('is_cash_bonus')
                    ->default(false)
                    ->helperText(fn (Forms\Get $get): string => $get('payment_method') === 'cash'
                        ? 'Cash method selected. You can mark this payment as a cash bonus.'
                        : 'Enable only when this payment qualifies for a cash bonus.'),
                Forms\Components\Toggle::make('is_single_transaction_bonus')
                    ->default(false)
                    ->helperText(function (Forms\Get $get): string {
                        $orderId = $get('order_id');

                        if (! $orderId) {
                            return 'Select an invoice first.';
                        }

                        $order = Order::find($orderId);
                        $amount = (float) ($get('amount') ?? 0);

                        if (! $order) {
                            return 'Invoice not found.';
                        }

                        return $amount + 0.0001 >= (float) $order->remaining_amount
                            ? 'Amount closes remaining balance in one transaction.'
                            : 'Amount does not close the invoice yet.';
                    }),
                Forms\Components\Textarea::make('bonus_notes')->rows(3)->nullable()->columnSpanFull(),
            ])->columns(2)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('order_id')->label('Invoice #'),
            Tables\Columns\TextColumn::make('pharmacy.pharmacy_name')->searchable(),
            Tables\Columns\TextColumn::make('amount')->money('USD', locale: 'en'),
            Tables\Columns\TextColumn::make('payment_date')->date(),
            Tables\Columns\TextColumn::make('payment_method'),
            Tables\Columns\IconColumn::make('is_cash_bonus')->boolean()->label('بونص الدفع النقدي'),
            Tables\Columns\IconColumn::make('is_single_transaction_bonus')->boolean()->label('بونص الدفع دفعة واحدة'),
            Tables\Columns\TextColumn::make('created_at')->dateTime(),
        ])->filters([
            SelectFilter::make('payment_method')->options(['cash'=>'Cash','bank_transfer'=>'Bank Transfer','cheque'=>'Cheque','other'=>'Other']),
            SelectFilter::make('pharmacy_id')->relationship('pharmacy', 'pharmacy_name')->label('الصيدلية'),
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
