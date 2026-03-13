<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;
use App\Models\ActivityLog;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalPc = User::where('role', 'pc')->count();
        $totalMwc = User::where('role', 'mwc')->count();
        $totalRanting = User::where('role', 'ranting')->count();
        $totalAllUsers = User::where('role', '!=', 'developer')->count();

        // Aktivitas User yang Sedang Login
        $aktivitasUser = ActivityLog::where('user_id', auth()->id())
            ->latest()
            ->take(10)
            ->get();

        // Sementara dummy dulu sampai tabel transaksi ada
        $totalTransaksiBulanIni = 450;

        $transaksiTerbaru = [
            [
                'kode' => 'TRX001',
                'tanggal' => '2023-10-01',
                'ranting' => 'Ranting A',
                'jenis' => 'Infaq UMKM',
                'nominal' => 'Rp 500.000',
                'status' => 'Tervalidasi',
            ],
            [
                'kode' => 'TRX002',
                'tanggal' => '2023-10-02',
                'ranting' => 'Ranting B',
                'jenis' => 'Infaq Toko',
                'nominal' => 'Rp 750.000',
                'status' => 'Proses',
            ],
        ];

        return view('developer.dashboard', compact(
            'totalPc',
            'totalMwc',
            'totalRanting',
            'totalAllUsers',
            'totalTransaksiBulanIni',
            'transaksiTerbaru',
            'aktivitasUser'
        ));
    }
}