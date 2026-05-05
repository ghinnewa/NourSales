<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PharmacyResource\Pages;
use App\Filament\Resources\PharmacyResource\RelationManagers\OrdersRelationManager;
use App\Filament\Resources\PharmacyResource\RelationManagers\PaymentsRelationManager;
use App\Models\Pharmacy;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PharmacyResource extends Resource
{
    protected static ?string $model = Pharmacy::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'إدارة العملاء';

    protected static ?string $navigationLabel = 'الصيدليات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات الصيدلية')
                    ->schema([
                        Forms\Components\TextInput::make('pharmacy_name')->required()->maxLength(255),
                        Forms\Components\TextInput::make('owner_name')->maxLength(255),
                        Forms\Components\TextInput::make('phone')->maxLength(50),
                        Forms\Components\TextInput::make('area')->maxLength(255),
                    ])->columns(2),
                Forms\Components\Section::make('الموقع')
                    ->schema([
                        Forms\Components\Textarea::make('address')->columnSpanFull(),
                        Forms\Components\TextInput::make('google_maps_link')->url()->maxLength(1000)->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('الملاحظات')
                    ->schema([
                        Forms\Components\Textarea::make('notes')->rows(4)->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('ملاحظات تجارية')
                    ->schema([
                        Forms\Components\Textarea::make('deal_notes')->rows(4)->nullable(),
                        Forms\Components\Textarea::make('payment_notes')->rows(4)->nullable(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pharmacy_name')->label('اسم الصيدلية')->searchable(),
                Tables\Columns\TextColumn::make('owner_name')->label('اسم المالك')->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('الهاتف')->searchable(),
                Tables\Columns\TextColumn::make('area')->label('المنطقة')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label('تاريخ الإضافة')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('area')
                    ->options(fn () => Pharmacy::query()->whereNotNull('area')->distinct()->orderBy('area')->pluck('area', 'area')->toArray()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            OrdersRelationManager::class,
            PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPharmacies::route('/'),
            'create' => Pages\CreatePharmacy::route('/create'),
            'view' => Pages\ViewPharmacy::route('/{record}'),
            'edit' => Pages\EditPharmacy::route('/{record}/edit'),
        ];
    }
}
