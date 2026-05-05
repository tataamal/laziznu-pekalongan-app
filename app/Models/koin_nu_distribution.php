<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class koin_nu_distribution extends Model
{
    protected $table = "koin_nu_distributions";

    protected $fillable = [
        "user_id",
        "distribution_code",
        "date",
        "jenis_pilar",
        "deskripsi",
        "jumlah_pentasarufan",
        "jumlah_penerima_manfaat",
        "keterangan",
        "file_dokumentasi",
        "approved_by",
        "approved_at",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
