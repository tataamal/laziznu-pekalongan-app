<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    protected $fillable = [
        'user_id',
        'transaction_code',
        'date',
        'event_name',
        'pilar_type',
        'cost_amount',
        'documentation_file',
        'status',
        'approved_by',
        'approved_at',
    ];  

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
