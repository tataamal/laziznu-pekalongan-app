<?php

namespace App\Repositories;

use App\Models\InfaqMwcTransaction;
use App\Models\InfaqMwcDistribution;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class InfaqMwcTransactionRepository
{
    private const CACHE_TTL = 900;

    private function getCacheVersion(string $scope): int
    {
        return (int) Cache::rememberForever("cache_version:infaq_mwc:{$scope}", fn() => 1);
    }

    public function bumpCacheVersion(string $scope): void
    {
        $key = "cache_version:infaq_mwc:{$scope}";

        if (!Cache::has($key)) {
            Cache::forever($key, 1);
            return;
        }

        Cache::increment($key);
    }

    public function getTransactions(
        int $wilayahId,
        ?string $startDate = null,
        ?string $endDate = null
    ): Collection {
        return InfaqMwcTransaction::query()
            ->where('wilayah_id', $wilayahId)
            ->when($startDate, fn($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('date', '<=', $endDate))
            ->orderByDesc('date')
            ->get();
    }

    // ============================================================
    // CRUD METHODS
    // ============================================================

    public function findById(int $id): ?InfaqMwcTransaction
    {
        return InfaqMwcTransaction::find($id);
    }

    public function create(array $data): InfaqMwcTransaction
    {
        $transaction = InfaqMwcTransaction::create($data);
        
        if (isset($data['wilayah_id'])) {
            $this->bumpCacheVersion("wilayah:{$data['wilayah_id']}");
        }

        return $transaction;
    }

    public function update(int $id, array $data): bool
    {
        $transaction = $this->findById($id);
        if (!$transaction) {
            return false;
        }

        $updated = $transaction->update($data);

        if ($updated && $transaction->wilayah_id) {
            $this->bumpCacheVersion("wilayah:{$transaction->wilayah_id}");
        }

        return $updated;
    }

    public function delete(int $id): bool
    {
        $transaction = $this->findById($id);
        if (!$transaction) {
            return false;
        }

        $wilayahId = $transaction->wilayah_id;
        $deleted = $transaction->delete();

        if ($deleted && $wilayahId) {
            $this->bumpCacheVersion("wilayah:{$wilayahId}");
        }

        return $deleted;
    }

    // ============================================================
    // Get Count Data Infaq MWC
    // ============================================================

    public function getTotalPemasukan(int $wilayahId, ?string $startDate = null, ?string $endDate = null): float
    {
        return InfaqMwcTransaction::where('wilayah_id', $wilayahId)
            ->when($startDate, fn($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('date', '<=', $endDate))
            ->sum('pemasukan_infaq_kotor');
    }
    
    public function getHakAmilMwc(
        int $wilayahId,
        ?string $startDate = null,
        ?string $endDate = null,
    ) {
        return InfaqMwcTransaction::where('wilayah_id', $wilayahId)
            ->when($startDate, fn($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('date', '<=', $endDate))
            ->sum('hak_amil');
    }

    public function getInfaqDapatDigunakanMwc(
        int $wilayahId,
        ?string $startDate = null,
        ?string $endDate = null,
    ) {
        return InfaqMwcTransaction::where('wilayah_id', $wilayahId)
            ->when($startDate, fn($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('date', '<=', $endDate))
            ->sum('infaq_yang_dapat_digunakan');
    }
}