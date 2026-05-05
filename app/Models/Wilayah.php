<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wilayah extends Model
{
    protected $table = 'wilayahs';

    protected $fillable = [
        'nama_wilayah',
        'alamat',
        'pic',
        'no_telp',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'wilayah_id');
    }

    public function data_ranting()
    {
        return $this->hasMany(DataRanting::class);
    }
}