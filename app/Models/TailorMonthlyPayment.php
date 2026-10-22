<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TailorMonthlyPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_no',
        'tailor_id',
        'payment_date',
        'month',
        'amount',
        'note',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'float',
    ];

    public function tailor()
    {
        return $this->belongsTo(Tailor::class);
    }
}
