<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class infaq_pc_transactions extends Model
{
    protected $table = "infaq_pc_transactions";
    protected $fillable = [
        "user_id",
        "transaction_code",
        "date",
        "jenis_infaq",
        "keterangan",
        "pemasukan_infaq_kotor",
        "jasa_petugas",
        "pemasukan_infaq_bersih",
        "hak_amil",
        "infaq_yang_dapat_digunakan",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
