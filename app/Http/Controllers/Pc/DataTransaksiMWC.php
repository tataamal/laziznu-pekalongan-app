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

        $transactionType = $request->query('transaction_type');

        $query = InfaqTransaction::with(['user', 'user.wilayah'])
            ->whereHas('user', function($query) use ($wilayahId) {
                $query->where('role', 'mwc');
                if ($wilayahId) {
                    $query->where('wilayah_id', $wilayahId);
                }
            })
            ->latest();

        if ($transactionType) {
            $query->where('transaction_type', $transactionType);
        }

        $items = $query->get();

        $pemasukans = $transactionType === 'Pengeluaran' ? collect() : $items->where('transaction_type', 'Pemasukan');
        $pengeluarans = $transactionType === 'Pemasukan' ? collect() : $items->where('transaction_type', 'Pengeluaran');

        return view('pc.data-transaksi-mwc', compact('items', 'pemasukans', 'pengeluarans', 'wilayahs', 'wilayahId'));
    }
}
