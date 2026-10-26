<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapitalDeposit extends Model
{
    protected $fillable = [
        'reference_no',
        'account_id',
        'amount',
        'type',
        'user_id',
        'note',
        'created_at',
    ];

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}

