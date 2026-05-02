<?php

namespace App\Services;

use App\Models\Order;

class OrderPaymentService
{
    public function recalculateOrder(Order $order, ?string $completedDate = null): void
    {
        $paid = (float) $order->payments()->sum('amount');
        $total = (float) $order->total_price;

        if ($paid + 0.0001 >= $total && $total > 0) {
            $closedDate = $completedDate ?: $this->findCompletionDate($order);
            $limit = $order->invoice_date->copy()->addMonths(2)->endOfDay();
            $closedAt = now()->parse($closedDate);
            $rate = $closedAt->lte($limit) ? 5.00 : 3.00;

            $order->update([
                'status' => 'closed',
                'closed_at' => $closedAt,
                'commission_rate' => $rate,
                'commission_amount' => round($total * ($rate / 100), 2),
            ]);

            return;
        }

        $order->update([
            'status' => $order->status === 'pending' ? 'pending' : 'delivered',
            'closed_at' => null,
            'commission_rate' => null,
            'commission_amount' => 0,
        ]);
    }

    private function findCompletionDate(Order $order): string
    {
        $running = 0;
        $target = (float) $order->total_price;
        foreach ($order->payments()->orderBy('payment_date')->orderBy('id')->get() as $payment) {
            $running += (float) $payment->amount;
            if ($running + 0.0001 >= $target) {
                return $payment->payment_date->toDateString();
            }
        }

        return now()->toDateString();
    }
}
