<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TailorDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_no',
        'tailor_id',
        'delivery_date',
        'delivery_amount',
        'percentage',
        'owner_get',
        'paid_amount',
        'due_amount',
        'payment_status',
        'note',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'delivery_amount' => 'float',
        'percentage' => 'float',
        'owner_get' => 'float',
        'paid_amount' => 'float',
        'due_amount' => 'float',
    ];

    public function tailor()
    {
        return $this->belongsTo(Tailor::class);
    }

    public function payments()
    {
        return $this->hasMany(TailorDeliveryPayment::class);
    }
}
