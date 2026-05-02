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
}
