<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KoinNuTransaction extends Model
{
    protected $table = "koin_nu_transactions";

    protected $fillable = [
        "user_id",
        "ranting_id",
        "wilayah_id",
        "transaction_code",
        "date",
        "jumlah_kaleng",
        "pemasukan_koin_nu_kotor",
        "jasa_petugas",
        "pemasukan_koin_nu_bersih",
        "koin_nu_ranting",
        "koin_nu_mwc",
        "koin_nu_pc",
        "dana_dapat_digunakan_ranting",
        "dana_dapat_digunakan_mwc",
        "dana_dapat_digunakan_pc",
        "hak_amil_ranting",
        "hak_amil_mwc",
        "hak_amil_pc",
        "status",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ranting()
    {
        return $this->belongsTo(DataRanting::class);
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }
}
