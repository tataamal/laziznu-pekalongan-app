<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MunfiqData extends Model
{
    protected $table = 'munfiq_data';

    protected $fillable = [
        'data_ranting_id',
        'nama',
        'kode_kaleng',
        'jenis_kelamin',
        'alamat',
        'status',
    ];

    public function data_ranting()
    {
        return $this->belongsTo(DataRanting::class);
    }
}
