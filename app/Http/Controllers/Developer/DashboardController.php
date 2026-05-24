<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Models\Wilayah;
use App\Http\Controllers\LandingController;
use App\Models\KoinNuTransaction;
use App\Models\KoinNuDistribution;
use App\Models\InfaqMwcTransaction;
use App\Models\InfaqMwcDistribution;
use App\Models\InfaqPcTransaction;
use App\Models\InfaqPcDistribution;

class DashboardController extends Controller
{
    public function index(Request $request): View
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

        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $wilayahId = $request->get('wilayah_id', 'all');
        $status = $request->get('status', 'validated');

        $wilayahs = Wilayah::all();
        $months = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        $landingCtrl = new LandingController();
        $incomeData = $landingCtrl->getIncomeData($month, $year, $status);
        $distributionData = $landingCtrl->getDistributionData($month, $year, $status);
        $infaqStats = $landingCtrl->getInfaqStatsData($month, $year, $wilayahId);

        return view('developer.dashboard', compact(
            'totalPc',
            'totalMwc',
            'totalRanting',
            'totalAllUsers',
            'aktivitasUser',
            'wilayahs',
            'months',
            'month',
            'year',
            'wilayahId',
            'status',
            'incomeData',
            'distributionData',
            'infaqStats'
        ));
    }
}