<?php

namespace App\Models;

use App\Services\InvoiceCalculationService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price_at_time',
        'line_total',
        'bonus_quantity',
        'bonus_notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price_at_time' => 'decimal:2',
        'line_total' => 'decimal:2',
        'bonus_quantity' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (OrderItem $item): void {
            $item->quantity = max(1, (int) $item->quantity);
            $item->bonus_quantity = max(0, (int) ($item->bonus_quantity ?? 0));
            $item->line_total = app(InvoiceCalculationService::class)->calculateLineTotal(
                $item->quantity,
                (float) $item->price_at_time,
            );
        });

        static::saved(function (OrderItem $item): void {
            if ($item->order) {
                app(InvoiceCalculationService::class)->recalculateAndPersistOrderTotals($item->order->fresh());
            }
        });

        static::deleted(function (OrderItem $item): void {
            if ($item->order_id && ($order = Order::find($item->order_id))) {
                app(InvoiceCalculationService::class)->recalculateAndPersistOrderTotals($order->fresh());
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
