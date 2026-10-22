<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TailorDeliveryPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tailor_delivery_id',
        'payment_date',
        'amount',
        'note',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'float',
    ];

    public function delivery()
    {
        return $this->belongsTo(TailorDelivery::class, 'tailor_delivery_id');
    }
}
