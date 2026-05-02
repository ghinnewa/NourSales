<?php
namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Services\InvoiceCalculationService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    public function mount(): void
    {
        parent::mount();

        if (request()->has('pharmacy_id')) {
            $this->form->fill([
                'pharmacy_id' => request()->integer('pharmacy_id'),
            ]);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['total_price'] = app(InvoiceCalculationService::class)->calculateInvoiceTotal($this->data['orderItems'] ?? []);

        if (($data['status'] ?? null) !== 'closed') {
            $data['closed_at'] = null;
            $data['commission_rate'] = null;
            $data['commission_amount'] = 0;
        } else {
            $closedAt = ! empty($data['closed_at']) ? Carbon::parse($data['closed_at']) : now();
            $invoiceDate = Carbon::parse($data['invoice_date'] ?? now()->toDateString());
            $rate = $closedAt->lte($invoiceDate->copy()->addMonths(2)->endOfDay()) ? 5.00 : 3.00;

            $data['closed_at'] = $closedAt;
            $data['commission_rate'] = $rate;
            $data['commission_amount'] = round($data['total_price'] * ($rate / 100), 2);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        app(InvoiceCalculationService::class)->recalculateAndPersistOrderTotals($this->record->fresh());
    }
}
