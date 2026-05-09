<?php
namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Carbon;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
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
            $data['commission_amount'] = round((float) $data['total_price'] * ($rate / 100), 2);
        }

        return $data;
    }
}
