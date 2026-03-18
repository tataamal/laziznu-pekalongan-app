<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\Wilayah;

class DataTransaksiRanting extends Controller
{
    public function index(Request $request)
    {
        $wilayahId = $request->query('wilayah_id');
        $wilayahs = Wilayah::orderBy('nama_wilayah')->get();

        $items = Income::with(['user', 'user.wilayah'])
            ->whereHas('user', function($query) use ($wilayahId) {
                $query->where('role', 'ranting');
                if ($wilayahId) {
                    $query->where('wilayah_id', $wilayahId);
                }
            })
            ->latest()
            ->get();

        return view('pc.data-transaksi-ranting', compact('items', 'wilayahs', 'wilayahId'));
    }
}
