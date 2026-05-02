<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Pharmacy;
use App\Services\OrderPaymentService;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        if (Pharmacy::count() === 0 || Order::count() === 0) return;

        $orders = Order::with('pharmacy')->get();
        foreach ($orders->take(2) as $order) {
            Payment::create(['pharmacy_id'=>$order->pharmacy_id,'order_id'=>$order->id,'amount'=>$order->total_price,'payment_date'=>now()->subDays(5)->toDateString(),'payment_method'=>'cash']);
        }
        foreach ($orders->slice(2,2) as $order) {
            Payment::create(['pharmacy_id'=>$order->pharmacy_id,'order_id'=>$order->id,'amount'=>round($order->total_price/2,2),'payment_date'=>now()->subDays(2)->toDateString(),'payment_method'=>'bank_transfer']);
        }

        $pharmacy = Pharmacy::first();
        Payment::create(['pharmacy_id'=>$pharmacy->id,'amount'=>100,'payment_date'=>now()->toDateString(),'payment_method'=>'other','notes'=>'Unlinked payment']);

        $service = app(OrderPaymentService::class);
        Order::all()->each(fn ($order) => $service->recalculateOrder($order));
    }
}
