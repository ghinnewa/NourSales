<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Sales';
    protected static ?string $navigationLabel = 'Orders / Invoices';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Invoice Details')->schema([
                Forms\Components\Select::make('pharmacy_id')->relationship('pharmacy', 'pharmacy_name')->searchable()->required(),
                Forms\Components\DatePicker::make('invoice_date')->required()->default(now()),
                Forms\Components\Select::make('status')->options([
                    'pending' => 'Pending','delivered' => 'Delivered','closed' => 'Closed','cancelled' => 'Cancelled',
                ])->required()->default('pending')->live(),
                Forms\Components\DateTimePicker::make('closed_at')->required(fn (Forms\Get $get): bool => $get('status') === 'closed')->visible(fn (Forms\Get $get): bool => $get('status') === 'closed'),
                Forms\Components\Textarea::make('notes')->columnSpanFull(),
            ])->columns(2),
            Forms\Components\Section::make('Order Items')->schema([
                Forms\Components\Repeater::make('orderItems')->relationship()->schema([
                    Forms\Components\Select::make('product_id')->label('Product')->options(fn () => Product::query()->get()->mapWithKeys(fn ($p) => [$p->id => trim($p->name.' | '.$p->brand.' | $'.number_format((float)$p->price,2))])->toArray())->searchable()->required()->live()->afterStateUpdated(function ($state, Forms\Set $set) {
                        $product = Product::find($state); if ($product) { $set('price_at_time', $product->price); $set('line_total', round($product->price * ((int)1), 2)); }
                    }),
                    Forms\Components\TextInput::make('quantity')->integer()->minValue(1)->default(1)->required()->live(),
                    Forms\Components\TextInput::make('price_at_time')->numeric()->minValue(0)->required()->live(),
                    Forms\Components\TextInput::make('line_total')->numeric()->minValue(0)->disabled()->dehydrated()->afterStateHydrated(function (Forms\Get $get, Forms\Set $set) { $set('line_total', round(((float)$get('quantity')) * ((float)$get('price_at_time')), 2)); })->live(),
                ])->columns(4)->defaultItems(1)->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                    $total = collect($get('orderItems'))->sum(fn ($i) => ((float)($i['quantity'] ?? 0)) * ((float)($i['price_at_time'] ?? 0))); $set('total_price', round($total, 2));
                })->live(),
            ]),
            Forms\Components\Section::make('Totals & Commission')->schema([
                Forms\Components\TextInput::make('total_price')->numeric()->disabled()->dehydrated(),
                Forms\Components\TextInput::make('commission_rate')->disabled()->dehydrated(),
                Forms\Components\TextInput::make('commission_amount')->numeric()->disabled()->dehydrated(),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id'),
            Tables\Columns\TextColumn::make('pharmacy.pharmacy_name')->searchable(),
            Tables\Columns\TextColumn::make('invoice_date')->date()->sortable(),
            Tables\Columns\TextColumn::make('total_price')->money('USD')->sortable(),
            Tables\Columns\TextColumn::make('status')->badge(),
            Tables\Columns\TextColumn::make('closed_at')->dateTime(),
            Tables\Columns\TextColumn::make('commission_rate')->formatStateUsing(fn ($state) => $state !== null ? number_format((float)$state, 2).'%' : '-'),
            Tables\Columns\TextColumn::make('commission_amount')->money('USD')->sortable(),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
        ])->filters([
            SelectFilter::make('status')->options(['pending'=>'Pending','delivered'=>'Delivered','closed'=>'Closed','cancelled'=>'Cancelled']),
        ])->actions([
            Tables\Actions\ViewAction::make(), Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),'create' => Pages\CreateOrder::route('/create'),'view' => Pages\ViewOrder::route('/{record}'),'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
