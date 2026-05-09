<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RequestInvoiceResource\Pages;
use App\Models\RequestInvoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RequestInvoiceResource extends Resource
{
    protected static ?string $model = RequestInvoice::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'الطلبات';
    protected static ?string $navigationLabel = 'Invoice Requests';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('requester_name')->required(),
            Forms\Components\TextInput::make('pharmacy_name')->required(),
            Forms\Components\TextInput::make('phone'),
            Forms\Components\Select::make('status')->options([
                'pending' => 'Pending','reviewed' => 'Reviewed','approved' => 'Approved','rejected' => 'Rejected','converted' => 'Converted',
            ])->required(),
            Forms\Components\DateTimePicker::make('submitted_at'),
            Forms\Components\Textarea::make('notes')->columnSpanFull(),
            Forms\Components\Textarea::make('whatsapp_message')->columnSpanFull(),
            Forms\Components\TextInput::make('total_amount')->numeric(),
            Forms\Components\Repeater::make('items')->relationship('items')->schema([
                Forms\Components\TextInput::make('product_name_snapshot')->disabled(),
                Forms\Components\TextInput::make('quantity')->numeric()->disabled(),
                Forms\Components\TextInput::make('product_price_snapshot')->numeric()->disabled(),
                Forms\Components\TextInput::make('line_total')->numeric()->disabled(),
            ])->columnSpanFull()->deletable(false)->addable(false)->reorderable(false),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('requester_name')->searchable(),
            Tables\Columns\TextColumn::make('pharmacy_name')->searchable(),
            Tables\Columns\TextColumn::make('phone')->toggleable(),
            Tables\Columns\TextColumn::make('status')->badge(),
            Tables\Columns\TextColumn::make('total_amount')->money('USD', locale: 'en'),
            Tables\Columns\TextColumn::make('submitted_at')->dateTime()->sortable(),
        ])->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRequestInvoices::route('/'),
            'view' => Pages\ViewRequestInvoice::route('/{record}'),
            'edit' => Pages\EditRequestInvoice::route('/{record}/edit'),
        ];
    }
}
