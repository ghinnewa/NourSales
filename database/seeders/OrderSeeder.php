<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Pharmacy;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $pharmacies = Pharmacy::all();
        $products = Product::all();

        if ($pharmacies->isEmpty() || $products->isEmpty()) {
            return;
        }

        $makeOrder = function ($status, $invoiceDate, $closedAt = null) use ($pharmacies, $products) {
            $order = Order::create([
                'pharmacy_id' => $pharmacies->random()->id,
                'invoice_date' => $invoiceDate,
                'status' => $status,
                'closed_at' => $closedAt,
                'notes' => 'Sample order',
                'total_price' => 0,
                'commission_amount' => 0,
            ]);

            $chosen = $products->random(min(2, $products->count()));
            $total = 0;
            foreach ($chosen as $product) {
                $qty = rand(1, 3);
                $line = round($qty * (float) $product->price, 2);
                $order->orderItems()->create(['product_id' => $product->id, 'quantity' => $qty, 'price_at_time' => $product->price, 'line_total' => $line]);
                $total += $line;
            }

            $rate = null;
            $commission = 0;
            if ($status === 'closed' && $closedAt) {
                $rate = now()->parse($closedAt)->lte(now()->parse($invoiceDate)->addMonths(2)->endOfDay()) ? 5 : 3;
                $commission = round($total * $rate / 100, 2);
            }

            $order->update(['total_price' => round($total, 2), 'commission_rate' => $rate, 'commission_amount' => $commission]);
        };

        $makeOrder('pending', now()->subDays(10)->toDateString());
        $makeOrder('delivered', now()->subDays(25)->toDateString());
        $makeOrder('closed', now()->subDays(30)->toDateString(), now()->subDays(10));
        $makeOrder('closed', now()->subMonths(4)->toDateString(), now()->subDays(5));
    }
}
