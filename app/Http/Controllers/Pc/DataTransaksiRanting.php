<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\Distribution;
use App\Models\Wilayah;

class DataTransaksiRanting extends Controller
{
    public function index(Request $request)
    {
        $wilayahId = $request->query('wilayah_id');
        $wilayahs = Wilayah::orderBy('nama_wilayah')->get();

        $transactionType = $request->query('transaction_type');

        $pemasukans = collect();
        if ($transactionType !== 'Pengeluaran') {
            $pemasukans = Income::with(['user', 'user.wilayah'])
                ->whereHas('user', function($query) use ($wilayahId) {
                    $query->where('role', 'ranting');
                    if ($wilayahId) {
                        $query->where('wilayah_id', $wilayahId);
                    }
                })
                ->latest()
                ->get();
        }

        $pengeluarans = collect();
        if ($transactionType !== 'Pemasukan') {
            $pengeluarans = Distribution::with(['user', 'user.wilayah'])
                ->whereHas('user', function($query) use ($wilayahId) {
                    $query->where('role', 'ranting');
                    if ($wilayahId) {
                        $query->where('wilayah_id', $wilayahId);
                    }
                })
                ->latest()
                ->get();
        }

        return view('pc.data-transaksi-ranting', compact('pemasukans', 'pengeluarans', 'wilayahs', 'wilayahId'));
    }
}
