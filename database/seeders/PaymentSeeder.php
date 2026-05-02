<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Payment;
use App\Services\OrderPaymentService;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::with('pharmacy')->get();
        foreach ($orders as $index => $order) {
            if ($index % 3 === 0) {
                Payment::create(['pharmacy_id'=>$order->pharmacy_id,'order_id'=>$order->id,'amount'=>$order->total_price,'payment_date'=>$order->invoice_date->copy()->addDays(20)->toDateString(),'payment_method'=>'cash']);
            } elseif ($index % 3 === 1) {
                Payment::create(['pharmacy_id'=>$order->pharmacy_id,'order_id'=>$order->id,'amount'=>round($order->total_price*0.4,2),'payment_date'=>$order->invoice_date->copy()->addDays(10)->toDateString(),'payment_method'=>'bank_transfer']);
            } else {
                Payment::create(['pharmacy_id'=>$order->pharmacy_id,'order_id'=>$order->id,'amount'=>$order->total_price,'payment_date'=>$order->invoice_date->copy()->addMonths(3)->toDateString(),'payment_method'=>'cheque']);
            }
        }

        $service = app(OrderPaymentService::class);
        Order::all()->each(fn ($order) => $service->recalculateOrder($order));
    }
}
