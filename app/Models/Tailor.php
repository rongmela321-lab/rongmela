<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tailor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone_number',
        'type',
        'percentage',
        'monthly_rate',
        'address',
        'is_active',
    ];

    protected $casts = [
        'percentage' => 'float',
        'monthly_rate' => 'float',
        'is_active' => 'boolean',
    ];

    public function deliveries()
    {
        return $this->hasMany(TailorDelivery::class);
    }

    public function monthlyPayments()
    {
        return $this->hasMany(TailorMonthlyPayment::class);
    }
}
