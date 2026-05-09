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

    public function calculateCustomerLineTotal(int $quantity, ?float $customerPrice): ?float
    {
        if ($customerPrice === null) {
            return null;
        }

        return round(max(0, $quantity) * max(0, $customerPrice), 2);
    }

    public function calculateInvoiceTotal(array $items): float
    {
        return round(collect($items)->sum(fn (array $item) => max(0, (float) ($item['line_total'] ?? 0))), 2);
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

        $closedAt = $order->closed_at ? Carbon::parse($order->closed_at) : now();
        $rate = $this->calculateCommissionRate($order, $closedAt);
        $updates['closed_at'] = $closedAt;
        $updates['commission_rate'] = $rate;
        $updates['commission_amount'] = round($invoiceTotal * ($rate / 100), 2);
    }

    public function recalculateAndPersistOrderTotals(Order $order): void
    {
        $order->loadMissing('orderItems', 'payments');

        $items = $order->orderItems->map(function (OrderItem $item): array {
            $lineTotal = $this->calculateLineTotal((int) $item->quantity, (float) $item->price_at_time);
            $customerLineTotal = $this->calculateCustomerLineTotal((int) $item->quantity, $item->customer_price !== null ? (float) $item->customer_price : null);

            $item->forceFill([
                'line_total' => $lineTotal,
                'customer_line_total' => $customerLineTotal,
            ])->saveQuietly();

            return ['line_total' => $lineTotal];
        })->all();

        $total = $this->calculateInvoiceTotal($items);
        $updates = ['total_price' => $total];
        $this->applyCommissionForOrder($order, $total, $updates);
        $order->forceFill($updates)->saveQuietly();
    }
}
