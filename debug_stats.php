<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\InfaqTransaction;
use Carbon\Carbon;

$month = '03';
$year = '2026';

$pcIncome = InfaqTransaction::whereHas('user', function ($q) {
        $q->where('role', 'pc');
    })
    ->where('transaction_type', 'Pemasukan')
    ->whereMonth('transaction_date', $month)
    ->whereYear('transaction_date', $year)
    ->sum('gross_amount');

$pcExpense = InfaqTransaction::whereHas('user', function ($q) {
        $q->where('role', 'pc');
    })
    ->where('transaction_type', 'Pengeluaran')
    ->whereMonth('transaction_date', $month)
    ->whereYear('transaction_date', $year)
    ->sum('gross_amount');

$mwcIncome = InfaqTransaction::whereHas('user', function ($q) {
        $q->where('role', 'mwc');
    })
    ->where('transaction_type', 'Pemasukan')
    ->whereMonth('transaction_date', $month)
    ->whereYear('transaction_date', $year)
    ->sum('gross_amount');

$mwcExpense = InfaqTransaction::whereHas('user', function ($q) {
        $q->where('role', 'mwc');
    })
    ->where('transaction_type', 'Pengeluaran')
    ->whereMonth('transaction_date', $month)
    ->whereYear('transaction_date', $year)
    ->sum('gross_amount');

$res = [
    'pc' => [
        'income' => (float)$pcIncome,
        'expense' => (float)$pcExpense,
        'ratio_income' => $pcIncome + $pcExpense > 0 ? round(($pcIncome / ($pcIncome + $pcExpense)) * 100, 1) : 0,
        'ratio_expense' => $pcIncome + $pcExpense > 0 ? round(($pcExpense / ($pcIncome + $pcExpense)) * 100, 1) : 0,
    ],
    'mwc' => [
        'income' => (float)$mwcIncome,
        'expense' => (float)$mwcExpense,
        'ratio_income' => $mwcIncome + $mwcExpense > 0 ? round(($mwcIncome / ($mwcIncome + $mwcExpense)) * 100, 1) : 0,
        'ratio_expense' => $mwcIncome + $mwcExpense > 0 ? round(($mwcExpense / ($mwcIncome + $mwcExpense)) * 100, 1) : 0,
    ]
];

echo json_encode($res, JSON_PRETTY_PRINT) . "\n";
