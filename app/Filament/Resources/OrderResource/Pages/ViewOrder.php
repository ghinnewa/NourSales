<?php
namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('addPayment')->label('إضافة دفعة')->url(fn() => \App\Filament\Resources\PaymentResource::getUrl('create', ['order_id' => $this->record->id])),
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('ملخص الفاتورة')->schema([
                TextEntry::make('id')->label('رقم الفاتورة'),
                TextEntry::make('pharmacy.pharmacy_name')->label('الصيدلية'),
                TextEntry::make('invoice_date')->label('تاريخ الفاتورة')->date(),
                TextEntry::make('total_price')->label('الإجمالي')->money('USD'),
                TextEntry::make('paid_amount')->label('المدفوع')->money('USD'),
                TextEntry::make('remaining_amount')->label('المتبقي')->money('USD'),
                TextEntry::make('status')->label('الحالة')->badge(),
                TextEntry::make('closed_at')->label('تاريخ الإغلاق')->dateTime(),
                TextEntry::make('commission_rate')->label('نسبة العمولة')->formatStateUsing(fn($state): string => $state ? "{$state}%" : '-'),
                TextEntry::make('commission_amount')->label('قيمة العمولة')->money('USD'),
            ])->columns(2),
            Section::make('الملاحظات')->schema([
                TextEntry::make('notes')->label('ملاحظات')->placeholder('-')->columnSpanFull(),
                TextEntry::make('deal_notes')->label('ملاحظات الاتفاق')->placeholder('-')->columnSpanFull(),
                TextEntry::make('offer_notes')->label('ملاحظات العرض')->placeholder('-')->columnSpanFull(),
                TextEntry::make('internal_notes')->label('ملاحظات داخلية')->placeholder('-')->columnSpanFull(),
                TextEntry::make('commission_explain')->state(fn(?Order $record): string => $record?->isFullyPaid() ? '5% خلال شهرين، 3% بعد شهرين.' : 'تُحسب العمولة بعد السداد الكامل.'),
            ]),
            Section::make('عناصر الفاتورة')->schema([RepeatableEntry::make('orderItems')->schema([
                TextEntry::make('product_code')->label('كود المنتج'),
                TextEntry::make('product_name')->label('اسم المنتج'),
                TextEntry::make('expiry_date')->label('تاريخ الانتهاء')->date(),
                TextEntry::make('quantity')->label('الكمية'),
                TextEntry::make('price_at_time')->label('السعر')->money('USD'),
                TextEntry::make('line_total')->label('الإجمالي')->money('USD'),
                TextEntry::make('customer_price')->label('سعر البيع للعميل')->money('USD'),
                TextEntry::make('customer_line_total')->label('إجمالي سعر العميل')->money('USD'),
            ])->columns(4)]),
            Section::make('سجل الدفعات')->schema([RepeatableEntry::make('payments')->schema([
                TextEntry::make('payment_date')->label('تاريخ الدفع')->date(),
                TextEntry::make('amount')->label('المبلغ')->money('USD'),
                TextEntry::make('payment_method')->label('طريقة الدفع'),
                TextEntry::make('notes')->label('ملاحظات')->placeholder('-'),
            ])->columns(4)]),
        ]);
    }
}
