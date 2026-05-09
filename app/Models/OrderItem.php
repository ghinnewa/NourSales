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
        'product_code',
        'product_name',
        'expiry_date',
        'quantity',
        'price_at_time',
        'line_total',
        'customer_price',
        'customer_line_total',
        'bonus_quantity',
        'bonus_notes',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'quantity' => 'integer',
        'price_at_time' => 'decimal:2',
        'line_total' => 'decimal:2',
        'customer_price' => 'decimal:2',
        'customer_line_total' => 'decimal:2',
        'bonus_quantity' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (OrderItem $item): void {
            $service = app(InvoiceCalculationService::class);
            $item->quantity = max(1, (int) $item->quantity);
            $item->bonus_quantity = max(0, (int) ($item->bonus_quantity ?? 0));
            $item->line_total = $service->calculateLineTotal($item->quantity, (float) $item->price_at_time);
            $item->customer_line_total = $service->calculateCustomerLineTotal($item->quantity, $item->customer_price !== null ? (float) $item->customer_price : null);
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
