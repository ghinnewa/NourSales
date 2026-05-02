<?php
namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return OrderDataCalculator::calculate($data);
    }

    protected function afterSave(): void
    {
        OrderDataCalculator::syncItems($this->record, $this->data['orderItems'] ?? []);
    }
}
