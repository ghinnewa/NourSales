<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\OrderPaymentService;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'pharmacy_id',
        'invoice_date',
        'total_price',
        'status',
        'closed_at',
        'commission_rate',
        'commission_amount',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'closed_at' => 'datetime',
        'total_price' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
    ];

    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
<<<<<<< HEAD
=======

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function paidAmount(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function remainingAmount(): float
    {
        return max(0, (float) $this->total_price - $this->paidAmount());
    }

    public function isFullyPaid(): bool
    {
        return $this->paidAmount() + 0.0001 >= (float) $this->total_price;
    }

    public function paymentStatus(): string
    {
        $paid = $this->paidAmount();
        if ($paid <= 0) return 'unpaid';
        if ($paid < (float) $this->total_price) return 'partially_paid';
        return 'paid';
    }

    public function recalculatePaymentStatus(?string $completedDate = null): void
    {
        app(OrderPaymentService::class)->recalculateOrder($this->fresh(), $completedDate);
    }
>>>>>>> 29d7a803bcadfd898c5fffc35350b9d60f2a165a
}
