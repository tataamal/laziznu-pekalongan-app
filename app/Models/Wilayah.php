<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wilayah extends Model
{
    protected $table = 'wilayah';

    protected $fillable = [
        'nama_wilayah',
        'alamat',
        'pic',
        'telp_pic',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'wilayah_id');
    }
}