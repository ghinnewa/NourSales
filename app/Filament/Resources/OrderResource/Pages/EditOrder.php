<?php
namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Services\InvoiceCalculationService;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['total_price'] = (float) ($data['total_price'] ?? 0);
        return $data;
    }

    protected function afterSave(): void
    {
        app(InvoiceCalculationService::class)->recalculateAndPersistOrderTotals($this->record);
    }
}
