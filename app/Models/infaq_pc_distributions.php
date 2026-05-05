<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class infaq_pc_distributions extends Model
{
    protected $table = "infaq_pc_distributions";
    protected $fillable = [
        "user_id",
        "distribution_code",
        "date",
        "jenis_pilar",
        "deskripsi",
        "jumlah_penerima_manfaat",
        "keterangan",
        "jumlah_total_distribusi",
        "file_dokumentasi",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
