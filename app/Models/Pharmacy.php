<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pharmacy extends Model
{
    use HasFactory;

    protected $fillable = [
        'pharmacy_name',
        'owner_name',
        'phone',
        'area',
        'address',
        'google_maps_link',
        'notes',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function totalOrdersValue(): float
    {
        return (float) $this->orders()->sum('total_price');
    }

    public function totalPaymentsValue(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function currentBalance(): float
    {
        return $this->totalOrdersValue() - $this->totalPaymentsValue();
    }
}
