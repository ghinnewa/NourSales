<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RequestInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_name',
        'pharmacy_name',
        'phone',
        'notes',
        'status',
        'whatsapp_message',
        'total_amount',
        'submitted_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'submitted_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(RequestInvoiceItem::class);
    }
}
