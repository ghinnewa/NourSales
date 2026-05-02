<?php

namespace App\Models;

use App\Services\OrderPaymentService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'pharmacy_id','invoice_date','total_price','status','closed_at','commission_rate','commission_amount','notes',
    ];

    protected $casts = [
        'invoice_date' => 'date','closed_at' => 'datetime','total_price' => 'decimal:2','commission_rate' => 'decimal:2','commission_amount' => 'decimal:2',
    ];

    protected $appends = ['paid_amount', 'remaining_amount', 'payment_status'];

    public function pharmacy(): BelongsTo { return $this->belongsTo(Pharmacy::class); }
    public function orderItems(): HasMany { return $this->hasMany(OrderItem::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }

    public function getPaidAmountAttribute(): float { return (float) $this->payments()->sum('amount'); }
    public function getRemainingAmountAttribute(): float { return max(0, round((float) $this->total_price - $this->paid_amount, 2)); }
    public function getPaymentStatusAttribute(): string {
        if ($this->paid_amount <= 0) return 'unpaid';
        if ($this->paid_amount + 0.0001 < (float) $this->total_price) return 'partially_paid';
        return 'paid';
    }

    public function isFullyPaid(): bool { return $this->payment_status === 'paid'; }
    public function isStillEligibleForFivePercent(): bool { return now()->lte($this->invoice_date->copy()->addMonths(2)->endOfDay()); }
    public function recalculateFinancials(?string $completedDate = null): void { app(OrderPaymentService::class)->recalculateOrder($this->fresh(), $completedDate); }
}
