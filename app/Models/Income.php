<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $fillable = [
        'user_id',
        'transaction_code',
        'date',
        'gross_profit',
        'operating_expenses',
        'net_income',
        'percentage',
        'allowed_budget',
        'hak_amil',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}