<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
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
        return $form->schema([
            Forms\Components\Section::make('Invoice Details')->schema([
                Forms\Components\Select::make('pharmacy_id')->relationship('pharmacy', 'pharmacy_name')->required()->searchable(),
                Forms\Components\DatePicker::make('invoice_date')->required()->default(now()->toDateString()),
                Forms\Components\Select::make('status')->options(['pending'=>'Pending','delivered'=>'Delivered','closed'=>'Closed','cancelled'=>'Cancelled'])->default('pending')->required(),
                Forms\Components\Textarea::make('notes')->columnSpanFull(),
            ])->columns(2),
            Forms\Components\Repeater::make('orderItems')->relationship()->schema([
                Forms\Components\Select::make('product_id')->label('Product')->options(Product::pluck('name','id'))->required(),
                Forms\Components\TextInput::make('quantity')->required()->numeric()->minValue(1)->default(1),
                Forms\Components\TextInput::make('price_at_time')->required()->numeric()->minValue(0),
                Forms\Components\TextInput::make('line_total')->required()->numeric()->minValue(0),
            ])->columns(4),
            Forms\Components\TextInput::make('total_price')->numeric()->required(),
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
