<?php

namespace App\Repositories;

use App\Models\InfaqPcTransaction;
use App\Models\InfaqMwcTransaction;
use App\Models\KoinNuTransaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class InfaqPcTransactionRepository
{
    private const CACHE_TTL = 900;

    private function getCacheVersion(): int
    {
        return (int) Cache::rememberForever("cache_version:infaq_pc", fn() => 1);
    }

    public function bumpCacheVersion(): void
    {
        $key = "cache_version:infaq_pc";

        if (!Cache::has($key)) {
            Cache::forever($key, 1);
            return;
        }

        Cache::increment($key);
    }

    /**
     * Ambil data transaksi infaq PC
     * @param string|null $startDate
     * @param string|null $endDate
     * @return Collection
     */
    public function getTransactions(
        ?string $startDate = null,
        ?string $endDate = null
    ): Collection {
        return InfaqPcTransaction::query()
            ->when($startDate, fn($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('date', '<=', $endDate))
            ->orderByDesc('date')
            ->get();
    }

    /**
     * Sum total pemasukan infaq PC
     * @param string|null $startDate
     * @param string|null $endDate
     * @param string|null $jenisInfaq
     * @return int
     */
    public function sumPemasukan(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $jenisInfaq = null
    ): int {
        return InfaqPcTransaction::query()
            ->when($startDate, fn($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('date', '<=', $endDate))
            ->when($jenisInfaq, fn($q) => $q->where('infaq_type', $jenisInfaq))
            ->sum('pemasukan_infaq_bersih');
    }

    /**
     * Sum Hak Amil PC
     * @param string|null $startDate
     * @param string|null $endDate
     * @return int
     */
    public function sumHakAmilPc(
        ?string $startDate = null,
        ?string $endDate = null
    ): int {
        return InfaqPcTransaction::query()
            ->when($startDate, fn($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('date', '<=', $endDate))
            ->sum('hak_amil');
    }

    /**
     * Sum dana dapat digunakan PC
     * @param string|null $startDate
     * @param string|null $endDate
     * @return int
     */
    public function sumDanaDapatDigunakanPc(
        ?string $startDate = null,
        ?string $endDate = null
    ): int {
        return InfaqPcTransaction::query()
            ->when($startDate, fn($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('date', '<=', $endDate))
            ->sum('infaq_yang_dapat_digunakan');
    }

    /**
     * Ambil list bulan untuk kebutuhan chart trend (default 6 bulan terakhir).
     * Format output: Y-m (contoh: 2026-05)
     */
    public function getTrendMonths(int $totalMonths = 6): Collection
    {
        $totalMonths = max(1, $totalMonths);

        return collect(range($totalMonths - 1, 0))
            ->map(fn($i) => now()->subMonths($i)->format('Y-m'));
    }

    /**
     * Ambil label bulan terjemahan untuk chart trend.
     * Format output default: M (contoh: Jan, Feb, Mar)
     */
    public function getTrendLabels(int $totalMonths = 6, string $outputFormat = 'M'): Collection
    {
        return $this->getTrendMonths($totalMonths)
            ->map(fn($month) => Carbon::createFromFormat('Y-m', $month)->translatedFormat($outputFormat));
    }

    public function sumTrendMwc(Collection $months): Collection
    {
        return $months->map(function ($month) {
            return InfaqMwcTransaction::query()
                ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$month])
                ->sum('pemasukan_infaq_bersih');
        });
    }

    public function sumTrendPcIncome(int $userId, Collection $months): Collection
    {
        return $months->map(function ($month) use ($userId) {
            return InfaqPcTransaction::query()
                ->where('user_id', $userId)
                ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$month])
                ->sum('pemasukan_infaq_bersih');
        });
    }

    public function sumTrendRanting(Collection $months): Collection
    {
        return $months->map(function ($month) {
            return KoinNuTransaction::query()
                ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$month])
                ->sum('koin_nu_ranting');
        });
    }

    public function getLatestTransactionsForPc(int $userId, int $limit = 10): Collection
    {
        return InfaqPcTransaction::query()
            ->where('user_id', $userId)
            ->latest()
            ->take($limit)
            ->get()
            ->map(function ($transaction) {
                return [
                    'kode' => $transaction->transaction_code,
                    'tanggal' => $transaction->date,
                    'user' => 'PC - Anda',
                    'role' => 'pc',
                    'jenis_label' => $transaction->jenis_infaq,
                    'jenis_filter' => 'pemasukan',
                    'nominal' => (float) $transaction->pemasukan_infaq_bersih,
                    'tipe' => 'Pemasukan',
                    'status' => 'validated',
                ];
            });
    }

    // ============================================================
    // CRUD METHODS
    // ============================================================

    public function findById(int $id): ?InfaqPcTransaction
    {
        return InfaqPcTransaction::find($id);
    }

    public function create(array $data): InfaqPcTransaction
    {
        $transaction = InfaqPcTransaction::create($data);
        $this->bumpCacheVersion();
        return $transaction;
    }

    public function update(int $id, array $data): bool
    {
        $transaction = $this->findById($id);
        if (!$transaction) {
            return false;
        }

        $updated = $transaction->update($data);

        if ($updated) {
            $this->bumpCacheVersion();
        }

        return $updated;
    }

    public function delete(int $id): bool
    {
        $transaction = $this->findById($id);
        if (!$transaction) {
            return false;
        }

        $deleted = $transaction->delete();

        if ($deleted) {
            $this->bumpCacheVersion();
        }

        return $deleted;
    }
}
