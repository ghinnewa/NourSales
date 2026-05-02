<?php

namespace App\Models;

use App\Services\OrderPaymentService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['pharmacy_id', 'order_id', 'amount', 'payment_date', 'payment_method', 'notes'];
    protected $casts = ['amount' => 'decimal:2', 'payment_date' => 'date'];

    protected static function booted(): void
    {
        static::saving(function (Payment $payment) {
            $order = Order::findOrFail($payment->order_id);
            $payment->pharmacy_id = $order->pharmacy_id;

            $sumOther = (float) Payment::where('order_id', $payment->order_id)->where('id', '!=', $payment->id)->sum('amount');
            $remaining = round((float) $order->total_price - $sumOther, 2);
            if ((float) $payment->amount > $remaining + 0.0001) {
                throw ValidationException::withMessages(['amount' => 'Payment amount cannot exceed remaining invoice balance.']);
            }
        });

        static::saved(function (Payment $payment) {
            $service = app(OrderPaymentService::class);
            $service->recalculateOrder($payment->order, $payment->payment_date?->toDateString());
            $originalOrderId = $payment->getOriginal('order_id');
            if ($originalOrderId && $originalOrderId !== $payment->order_id) {
                if ($oldOrder = Order::find($originalOrderId)) $service->recalculateOrder($oldOrder);
            }
        });

        static::deleted(function (Payment $payment) {
            if ($payment->order_id && ($order = Order::find($payment->order_id))) app(OrderPaymentService::class)->recalculateOrder($order);
        });
    }

    public function pharmacy(): BelongsTo { return $this->belongsTo(Pharmacy::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
}
