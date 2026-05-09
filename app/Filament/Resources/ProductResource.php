<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'المخزون';
    protected static ?string $navigationLabel = 'المنتجات';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات المنتج')->schema([
                Forms\Components\TextInput::make('product_code')->label('كود المنتج')->maxLength(255),
                Forms\Components\TextInput::make('name')->label('اسم المنتج')->required()->maxLength(255),
                Forms\Components\TextInput::make('brand')->label('العلامة التجارية')->maxLength(255),
                Forms\Components\TextInput::make('price')->label('السعر')->required()->numeric()->minValue(0)->prefix('$'),
                Forms\Components\TextInput::make('customer_price')->label('سعر البيع للعميل')->numeric()->minValue(0)->prefix('$'),
                Forms\Components\FileUpload::make('image')->label('صورة المنتج')->image()->disk('public')->directory('product-images')->imageEditor(),
                Forms\Components\Textarea::make('description')->label('الوصف')->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('product_code')->label('كود المنتج')->searchable(),
            Tables\Columns\TextColumn::make('name')->label('اسم المنتج')->searchable(),
            Tables\Columns\TextColumn::make('brand')->label('العلامة التجارية')->searchable(),
            Tables\Columns\TextColumn::make('price')->label('السعر')->money('USD')->sortable(),
            Tables\Columns\TextColumn::make('customer_price')->label('سعر البيع للعميل')->money('USD')->sortable(),
        ])->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
