<?php
namespace App\Filament\Resources\OrderResource\Pages;

use App\Models\Order;
use Illuminate\Validation\ValidationException;

class OrderDataCalculator
{
    public static function calculate(array $data): array
    {
        $items = $data['orderItems'] ?? [];
        $total = 0;
        foreach ($items as &$item) {
            $line = round(((int)($item['quantity'] ?? 0)) * ((float)($item['price_at_time'] ?? 0)), 2);
            $item['line_total'] = $line;
            $total += $line;
        }
        $data['orderItems'] = $items;
        $data['total_price'] = round($total, 2);
        $status = $data['status'] ?? 'pending';
        if ($status === 'closed') {
            if (empty($data['closed_at'])) throw ValidationException::withMessages(['closed_at' => 'closed_at is required when status is closed.']);
            $limit = now()->parse($data['invoice_date'] ?? now())->addMonths(2)->endOfDay();
            $closedAt = now()->parse($data['closed_at']);
            $rate = $closedAt->lte($limit) ? 5.00 : 3.00;
            $data['commission_rate'] = $rate;
            $data['commission_amount'] = round($data['total_price'] * $rate / 100, 2);
        } else {
            $data['closed_at'] = null;
            $data['commission_rate'] = null;
            $data['commission_amount'] = 0;
        }
        if ($status === 'cancelled') {
            $data['commission_rate'] = null;
            $data['commission_amount'] = 0;
        }
        return $data;
    }

    public static function syncItems(Order $order, array $items): void
    {
        $order->orderItems()->delete();
        foreach ($items as $item) {
            $order->orderItems()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price_at_time' => $item['price_at_time'],
                'line_total' => round(((int)$item['quantity']) * ((float)$item['price_at_time']), 2),
            ]);
        }
    }
}
