<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Product;
use App\Services\InvoiceCalculationService;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'المبيعات';
    protected static ?string $navigationLabel = 'الفواتير';
    protected static ?string $modelLabel = 'فاتورة';
    protected static ?string $pluralModelLabel = 'الفواتير';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Section::make('تفاصيل الفاتورة')->schema([
                Forms\Components\Select::make('pharmacy_id')->label('الصيدلية')->relationship('pharmacy', 'pharmacy_name')->required()->searchable()->preload(),
                Forms\Components\DatePicker::make('invoice_date')->label('تاريخ الفاتورة')->required()->default(now()->toDateString()),
                Forms\Components\Select::make('status')->label('الحالة')->options(['pending' => 'قيد الانتظار', 'delivered' => 'تم التسليم', 'closed' => 'مغلقة', 'cancelled' => 'ملغاة'])->default('pending')->live()->required(),
                Forms\Components\DateTimePicker::make('closed_at')->label('تاريخ الإغلاق')->seconds(false)->visible(fn(Get $get): bool => $get('status') === 'closed'),
                Forms\Components\TextInput::make('total_price')->label('إجمالي الفاتورة')->disabled()->dehydrated(),
                Forms\Components\Textarea::make('notes')->label('ملاحظات')->rows(3)->nullable()->columnSpanFull(),
                Forms\Components\Textarea::make('deal_notes')->label('ملاحظات الاتفاق')->rows(3)->nullable()->columnSpanFull(),
                Forms\Components\Textarea::make('offer_notes')->label('ملاحظات العرض')->rows(3)->nullable()->columnSpanFull(),
                Forms\Components\Textarea::make('internal_notes')->label('ملاحظات داخلية')->rows(3)->nullable()->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('عناصر الفاتورة')->schema([
                Forms\Components\Repeater::make('orderItems')
                    ->relationship()
                    ->defaultItems(1)
                    ->reorderable(false)
                    ->live()
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('المنتج')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->exists('products', 'id')
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set): void {
                                if (! $state) {
                                    return;
                                }

                                $product = Product::find($state);
                                if (! $product) {
                                    return;
                                }

                                $set('product_code', $product->product_code);
                                $set('product_name', $product->name);
                                $set('price_at_time', (float) $product->price);
                                $set('customer_price', $product->customer_price !== null ? (float) $product->customer_price : null);
                            }),
                        Forms\Components\TextInput::make('product_code')->label('كود المنتج')->maxLength(255),
                        Forms\Components\TextInput::make('product_name')->label('اسم المنتج')->required()->maxLength(255),
                        Forms\Components\DatePicker::make('expiry_date')->label('تاريخ الانتهاء')->nullable(),
                        Forms\Components\TextInput::make('quantity')->label('الكمية')->integer()->minValue(1)->default(1)->required()->live(),
                        Forms\Components\TextInput::make('price_at_time')->label('السعر')->numeric()->minValue(0)->required()->live(),
                        Forms\Components\TextInput::make('line_total')->label('الإجمالي')->numeric()->minValue(0)->disabled()->dehydrated(),
                        Forms\Components\TextInput::make('customer_price')->label('سعر البيع للعميل')->numeric()->minValue(0)->live(),
                        Forms\Components\TextInput::make('customer_line_total')->label('إجمالي سعر العميل')->numeric()->minValue(0)->disabled()->dehydrated(),
                    ])->columns(3)
                    ->afterStateUpdated(function (?array $state, Set $set): void {
                        $service = app(InvoiceCalculationService::class);
                        $items = collect($state ?? [])->map(function (array $item) use ($service): array {
                            $quantity = max(1, (int) ($item['quantity'] ?? 1));
                            $price = (float) ($item['price_at_time'] ?? 0);
                            $customerPrice = ($item['customer_price'] ?? null) !== null ? (float) $item['customer_price'] : null;
                            return [
                                ...$item,
                                'line_total' => $service->calculateLineTotal($quantity, $price),
                                'customer_line_total' => $service->calculateCustomerLineTotal($quantity, $customerPrice),
                            ];
                        })->all();

                        $set('orderItems', $items);
                        $set('total_price', $service->calculateInvoiceTotal($items));
                    }),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->label('Invoice #'),
            Tables\Columns\TextColumn::make('pharmacy.pharmacy_name')->label('الصيدلية')->searchable(),
            Tables\Columns\TextColumn::make('invoice_date')->label('تاريخ الفاتورة')->date(),
            Tables\Columns\TextColumn::make('order_items_count')->label('عدد العناصر')->counts('orderItems'),
            Tables\Columns\TextColumn::make('total_price')->label('إجمالي الفاتورة')->money('USD'),
            Tables\Columns\TextColumn::make('paid_amount')->label('المدفوع')->money('USD'),
            Tables\Columns\TextColumn::make('remaining_amount')->label('المتبقي')->money('USD'),
            Tables\Columns\TextColumn::make('status')->label('الحالة')->badge(),
            Tables\Columns\TextColumn::make('commission_amount')->label('العمولة')->money('USD'),
            Tables\Columns\TextColumn::make('created_at')->label('تاريخ الإنشاء')->dateTime(),
        ])->actions([
            Tables\Actions\ViewAction::make(), Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make(),
            Tables\Actions\Action::make('addPayment')->label('إضافة دفعة')->url(fn(Order $record) => PaymentResource::getUrl('create', ['order_id' => $record->id])),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListOrders::route('/'), 'create' => Pages\CreateOrder::route('/create'), 'view' => Pages\ViewOrder::route('/{record}'), 'edit' => Pages\EditOrder::route('/{record}/edit')];
    }
}
