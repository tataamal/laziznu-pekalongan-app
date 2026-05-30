<?php

namespace App\Repositories;

use App\Models\InfaqPcDistribution;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class InfaqPcDistributionRepository
{
    private const CACHE_TTL = 900;

    private function getCacheVersion(): int
    {
        return (int) Cache::rememberForever("cache_version:infaq_pc_dist", fn() => 1);
    }

    public function bumpCacheVersion(): void
    {
        $key = "cache_version:infaq_pc_dist";

        if (!Cache::has($key)) {
            Cache::forever($key, 1);
            return;
        }

        Cache::increment($key);
    }

    /**
     * Summary of getDistributions
     * @param string $jenisPilar
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getDistributions(
        ?string $jenisPilar = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): EloquentCollection {
        return InfaqPcDistribution::query()
            ->when($jenisPilar, fn($q) => $q->where('jenis_pilar', $jenisPilar))
            ->when($startDate, fn($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('date', '<=', $endDate))
            ->orderByDesc('date')
            ->get();
    }

    /**
     * Sum total distribusi infaq PC
     * @param string|null $jenisPilar,
     * @param string|null $startDate,
     * @param string|null $endDate,
     * @return int
     */
    public function sumDistributions(
        ?string $jenisPilar = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): int {
        return InfaqPcDistribution::query()
            ->when($jenisPilar, fn($q) => $q->where('jenis_pilar', $jenisPilar))
            ->when($startDate, fn($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('date', '<=', $endDate))
            ->sum('jumlah_total_distribusi');
    }

    public function getExpenseDistributionForUser(int $userId): array
    {
        $expenseData = InfaqPcDistribution::query()
            ->where('user_id', $userId)
            ->select('jenis_pilar')
            ->selectRaw('SUM(jumlah_total_distribusi) as total')
            ->groupBy('jenis_pilar')
            ->get();

        return [
            'labels' => $expenseData->pluck('jenis_pilar'),
            'data' => $expenseData->pluck('total'),
        ];
    }

    public function sumTrendPcExpense(Collection $months, int $userId): Collection
    {
        return $months->map(function ($month) use ($userId) {
            return InfaqPcDistribution::query()
                ->where('user_id', $userId)
                ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$month])
                ->sum('jumlah_total_distribusi');
        });
    }

    public function getDistributionGroupedByPilarPc(): array
    {
        $data = InfaqPcDistribution::query()
            ->select('jenis_pilar')
            ->selectRaw('SUM(jumlah_total_distribusi) as total')
            ->groupBy('jenis_pilar')
            ->having('total', '>', 0)
            ->get();

        return [
            'labels' => $data->pluck('jenis_pilar'),
            'data' => $data->pluck('total'),
        ];
    }

    public function getLatestDistributions(int $limit = 50): EloquentCollection
    {
        return InfaqPcDistribution::query()
            ->with('user')
            ->orderByDesc('date')
            ->take($limit)
            ->get();
    }

    // ============================================================
    // CRUD METHODS
    // ============================================================

    public function findById(int $id): ?InfaqPcDistribution
    {
        return InfaqPcDistribution::find($id);
    }

    public function create(array $data): InfaqPcDistribution
    {
        $distribution = InfaqPcDistribution::create($data);
        $this->bumpCacheVersion();
        return $distribution;
    }

    public function update(int $id, array $data): bool
    {
        $distribution = $this->findById($id);
        if (!$distribution) {
            return false;
        }

        $updated = $distribution->update($data);

        if ($updated) {
            $this->bumpCacheVersion();
        }

        return $updated;
    }

    public function delete(int $id): bool
    {
        $distribution = $this->findById($id);
        if (!$distribution) {
            return false;
        }

        $deleted = $distribution->delete();

        if ($deleted) {
            $this->bumpCacheVersion();
        }

        return $deleted;
    }
}
