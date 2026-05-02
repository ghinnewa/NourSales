<?php
namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Models\Order;
use Filament\Resources\Pages\CreateRecord;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    public function mount(): void
    {
        parent::mount();

        $pharmacyId = request()->integer('pharmacy_id');

        if ($pharmacyId) {
            $this->form->fill([
                'pharmacy_id' => $pharmacyId,
            ]);
        }

        $orderId = request()->integer('order_id');
        if ($orderId && ($order = Order::find($orderId))) {
            $this->form->fill([
                'order_id' => $orderId,
                'pharmacy_id' => $order->pharmacy_id,
                'amount' => $order->remaining_amount,
            ]);
        }
    }
}
