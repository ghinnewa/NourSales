<?php
namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    public function mount(): void
    {
        parent::mount();

        if (request()->has('pharmacy_id')) {
            $this->form->fill(['pharmacy_id' => request()->integer('pharmacy_id')]);
        }
    }
}
