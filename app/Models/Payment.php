<?php

namespace App\Models;

use App\Services\OrderPaymentService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['pharmacy_id', 'order_id', 'amount', 'payment_date', 'payment_method', 'notes'];

    protected $casts = ['amount' => 'decimal:2', 'payment_date' => 'date'];

    protected static function booted(): void
    {
        static::saved(function (Payment $payment) {
            $service = app(OrderPaymentService::class);
            if ($payment->order_id) {
                $service->recalculateOrder($payment->order, $payment->payment_date->toDateString());
            }
            $originalOrderId = $payment->getOriginal('order_id');
            if ($originalOrderId && $originalOrderId !== $payment->order_id) {
                $oldOrder = Order::find($originalOrderId);
                if ($oldOrder) $service->recalculateOrder($oldOrder);
            }
        });

        static::deleted(function (Payment $payment) {
            if ($payment->order_id) {
                $order = Order::find($payment->order_id);
                if ($order) app(OrderPaymentService::class)->recalculateOrder($order);
            }
        });
    }

    public function pharmacy(): BelongsTo { return $this->belongsTo(Pharmacy::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
}
