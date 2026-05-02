<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Product;
use App\Services\InvoiceCalculationService;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Sales';
    protected static ?string $navigationLabel = 'Invoices';
    protected static ?string $modelLabel = 'Invoice';
    protected static ?string $pluralModelLabel = 'Invoices';

    public static function form(Form $form): Form
    {
        $recalculateLineTotal = static function (Get $get, Set $set): void {
            $set(
                'line_total',
                app(InvoiceCalculationService::class)->calculateLineTotal(
                    (int) ($get('quantity') ?? 0),
                    (float) ($get('price_at_time') ?? 0),
                ),
            );
        };

        return $form->schema([
            Forms\Components\Section::make('Invoice Details')->schema([
                Forms\Components\Select::make('pharmacy_id')->relationship('pharmacy', 'pharmacy_name')->required()->searchable()->preload(),
                Forms\Components\DatePicker::make('invoice_date')->required()->default(now()->toDateString()),
                Forms\Components\Select::make('status')
                    ->options(['pending' => 'Pending', 'delivered' => 'Delivered', 'closed' => 'Closed', 'cancelled' => 'Cancelled'])
                    ->default('pending')
                    ->live()
                    ->required(),
                Forms\Components\DateTimePicker::make('closed_at')
                    ->label('Closed At')
                    ->seconds(false)
                    ->visible(fn (Get $get): bool => $get('status') === 'closed'),
            ])->columns(2),

            Forms\Components\Section::make('Invoice Items')->schema([
                Forms\Components\Repeater::make('orderItems')
                    ->relationship()
                    ->defaultItems(1)
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Product')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->afterStateUpdated(function ($state, Get $get, Set $set) use ($recalculateLineTotal): void {
                                $price = (float) (Product::find($state)?->price ?? 0);
                                $set('price_at_time', $price);
                                $recalculateLineTotal($get, $set);
                            }),
                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->integer()
                            ->minValue(1)
                            ->default(1)
                            ->live(debounce: 300)
                            ->afterStateUpdated($recalculateLineTotal),
                        Forms\Components\TextInput::make('price_at_time')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$')
                            ->live(debounce: 300)
                            ->afterStateUpdated($recalculateLineTotal),
                        Forms\Components\TextInput::make('line_total')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->prefix('$'),
                        Forms\Components\TextInput::make('bonus_quantity')
                            ->required()
                            ->integer()
                            ->minValue(0)
                            ->default(0)
                            ->helperText(function (Get $get): ?string {
                                $productId = $get('product_id');

                                if (! $productId) {
                                    return null;
                                }

                                $isEligible = Product::query()->whereKey($productId)->value('bonus_eligible');

                                return $isEligible
                                    ? 'Selected product is bonus eligible.'
                                    : 'Selected product is not marked as bonus eligible.';
                            }),
                        Forms\Components\Textarea::make('bonus_notes')
                            ->rows(2)
                            ->maxLength(65535)
                            ->nullable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Forms\Components\Placeholder::make('invoice_total_preview')
                    ->label('Invoice Total')
                    ->content(function (Get $get): string {
                        $total = app(InvoiceCalculationService::class)->calculateInvoiceTotal($get('orderItems') ?? []);

                        return '$' . number_format($total, 2);
                    }),
            ]),

            Forms\Components\Section::make('Notes & Deal Details')->schema([
                Forms\Components\Textarea::make('notes')->rows(3)->nullable(),
                Forms\Components\Textarea::make('deal_notes')->rows(4)->nullable(),
                Forms\Components\Textarea::make('internal_notes')->rows(4)->nullable(),
            ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->label('Invoice #'), Tables\Columns\TextColumn::make('pharmacy.pharmacy_name'), Tables\Columns\TextColumn::make('invoice_date')->date(),
            Tables\Columns\TextColumn::make('total_price')->money('USD'), Tables\Columns\TextColumn::make('paid_amount')->money('USD'), Tables\Columns\TextColumn::make('remaining_amount')->money('USD'),
            Tables\Columns\TextColumn::make('payment_status')->badge(), Tables\Columns\TextColumn::make('status')->badge()->label('Invoice Status'), Tables\Columns\TextColumn::make('commission_amount')->money('USD'), Tables\Columns\TextColumn::make('created_at')->dateTime(),
        ])->actions([
            Tables\Actions\ViewAction::make(), Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make(),
            Tables\Actions\Action::make('addPayment')->label('Add Payment')->url(fn(Order $record) => PaymentResource::getUrl('create', ['order_id'=>$record->id])),
        ]);
    }

    public static function getPages(): array
    {
        return ['index'=>Pages\ListOrders::route('/'),'create'=>Pages\CreateOrder::route('/create'),'view'=>Pages\ViewOrder::route('/{record}'),'edit'=>Pages\EditOrder::route('/{record}/edit')];
    }
}
