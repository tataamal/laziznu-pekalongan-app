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
        'hak_amil_mwc',
        'hak_amil_pc',
        'status',
        'approved_by',
        'approved_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}