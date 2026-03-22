<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataRanting extends Model
{
    protected $table = 'data_ranting';

    protected $fillable = [
        'wilayah_id',
        'nama',
        'kode_ranting',
    ];

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function munfiq_data()
    {
        return $this->hasMany(MunfiqData::class);
    }
}
