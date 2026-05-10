<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KoinNuDistribution extends Model
{
    protected $table = "Koin_nu_distributions";

    protected $fillable = [
        "user_id",
        "ranting_id",
        "wilayah_id",
        "distribution_code",
        "date",
        "jenis_pilar",
        "deskripsi",
        "jumlah_pentasarufan_ranting",
        "jumlah_pentasarufan_mwc",
        "jumlah_pentasarufan_pc",
        "jumlah_penerima_manfaat_ranting",
        "jumlah_penerima_manfaat_mwc",
        "jumlah_penerima_manfaat_pc",
        "file_dokumentasi",
        "status",
        "approved_by",
        "approved_at",
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
