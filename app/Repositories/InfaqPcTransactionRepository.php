<?php

namespace App\Repositories;

use App\Models\InfaqPcTransaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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
