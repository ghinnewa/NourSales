<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class InvoiceCalculationService
{
    public function calculateLineTotal(int $quantity, float $priceAtTime): float
    {
        return round(max(0, $quantity) * max(0, $priceAtTime), 2);
    }

    public function calculateInvoiceTotal(array $items): float
    {
        $total = 0;

        foreach ($items as $item) {
            $quantity = max(0, (int) ($item['quantity'] ?? 0));
            $priceAtTime = max(0, (float) ($item['price_at_time'] ?? 0));
            $lineTotal = array_key_exists('line_total', $item)
                ? round((float) $item['line_total'], 2)
                : $this->calculateLineTotal($quantity, $priceAtTime);

            $total += max(0, $lineTotal);
        }

        return round($total, 2);
    }

    public function calculatePaidAmount(Order $order): float
    {
        return round((float) $order->payments()->sum('amount'), 2);
    }

    public function calculateRemainingAmount(float $invoiceTotal, float $paidAmount): float
    {
        return max(0, round($invoiceTotal - $paidAmount, 2));
    }

    public function calculateCommissionRate(Order $order, Carbon $closedAt): float
    {
        $limit = Carbon::parse($order->invoice_date)->addMonths(2)->endOfDay();

        return $closedAt->lte($limit) ? 5.00 : 3.00;
    }

    public function applyCommissionForOrder(Order $order, float $invoiceTotal, array &$updates): void
    {
        if ($order->status !== 'closed') {
            $updates['closed_at'] = null;
            $updates['commission_rate'] = null;
            $updates['commission_amount'] = 0;

            return;
        }

        $closedAt = $order->closed_at
            ? Carbon::parse($order->closed_at)
            : now();

        $rate = $this->calculateCommissionRate($order, $closedAt);

        $updates['closed_at'] = $closedAt;
        $updates['commission_rate'] = $rate;
        $updates['commission_amount'] = round($invoiceTotal * ($rate / 100), 2);
    }

    public function recalculateAndPersistOrderTotals(Order $order): void
    {
        $order->loadMissing('orderItems', 'payments');

        $items = $order->orderItems
            ->map(fn (OrderItem $item): array => [
                'quantity' => $item->quantity,
                'price_at_time' => $item->price_at_time,
                'line_total' => $item->line_total,
            ])
            ->all();

        $total = $this->calculateInvoiceTotal($items);

        $updates = [
            'total_price' => $total,
        ];

        $this->applyCommissionForOrder($order, $total, $updates);

        $order->forceFill($updates)->saveQuietly();
    }
}
