<?php
namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return OrderDataCalculator::calculate($data);
    }

    protected function afterCreate(): void
    {
        OrderDataCalculator::syncItems($this->record, $this->data['orderItems'] ?? []);
    }
}
