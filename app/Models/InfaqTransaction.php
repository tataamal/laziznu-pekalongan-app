<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfaqTransaction extends Model
{
    protected $table = 'infaq_transaction';

    protected $fillable = [
        'user_id',
        'transaction_code',
        'transaction_date',
        'transaction_type',
        'infaq_type',
        'description',
        'gross_amount',
        'percentage',
        'net_amount',
        'allowed_budget',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
