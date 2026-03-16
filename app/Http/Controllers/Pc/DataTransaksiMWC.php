<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InfaqTransaction;
use App\Models\Wilayah;

class DataTransaksiMWC extends Controller
{
    public function index(Request $request)
    {
        $wilayahId = $request->query('wilayah_id');
        $wilayahs = Wilayah::orderBy('nama_wilayah')->get();

        $items = InfaqTransaction::with(['user', 'user.wilayah'])
            ->whereHas('user', function($query) use ($wilayahId) {
                $query->where('role', 'mwc');
                if ($wilayahId) {
                    $query->where('wilayah_id', $wilayahId);
                }
            })
            ->latest()
            ->get();

        return view('pc.data-transaksi-mwc', compact('items', 'wilayahs', 'wilayahId'));
    }
}
