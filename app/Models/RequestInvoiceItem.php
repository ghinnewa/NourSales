<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestInvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_invoice_id',
        'product_id',
        'product_name_snapshot',
        'product_price_snapshot',
        'quantity',
        'line_total',
    ];

    protected $casts = [
        'product_price_snapshot' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function requestInvoice(): BelongsTo
    {
        return $this->belongsTo(RequestInvoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
