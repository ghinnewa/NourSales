<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Form;
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

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات الفاتورة')->schema([
                Forms\Components\Select::make('pharmacy_id')->label('الصيدلية')->relationship('pharmacy', 'pharmacy_name')->required()->searchable()->preload(),
                Forms\Components\DatePicker::make('invoice_date')->label('تاريخ الفاتورة')->required()->default(now()->toDateString()),
                Forms\Components\TextInput::make('total_price')->label('إجمالي الفاتورة')->required()->numeric()->minValue(0.01)->prefix('$'),
                Forms\Components\Select::make('status')
                    ->label('الحالة')
                    ->options(['pending' => 'قيد الانتظار', 'delivered' => 'تم التسليم', 'closed' => 'مغلقة', 'cancelled' => 'ملغاة'])
                    ->default('pending')
                    ->live()
                    ->required(),
                Forms\Components\DateTimePicker::make('closed_at')
                    ->label('تاريخ الإغلاق')
                    ->seconds(false)
                    ->visible(fn(Get $get): bool => $get('status') === 'closed'),
                Forms\Components\Textarea::make('notes')->label('ملاحظات')->rows(3)->nullable()->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('ملاحظات العرض / البونص / الاتفاق')->schema([
                Forms\Components\Textarea::make('deal_notes')->label('ملاحظات الاتفاق')->rows(3)->nullable(),
                Forms\Components\Textarea::make('offer_notes')->label('ملاحظات العرض / البونص')->rows(3)->nullable(),
                Forms\Components\Textarea::make('internal_notes')->label('ملاحظات داخلية')->rows(3)->nullable(),
            ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->label('Invoice #'),
            Tables\Columns\TextColumn::make('pharmacy.pharmacy_name')->label('الصيدلية')->searchable(),
            Tables\Columns\TextColumn::make('invoice_date')->label('تاريخ الفاتورة')->date(),
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
