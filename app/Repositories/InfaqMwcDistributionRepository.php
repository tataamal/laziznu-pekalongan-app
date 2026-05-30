<?php

namespace App\Repositories;

use App\Models\InfaqMwcDistribution;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class InfaqMwcDistributionRepository
{
    private const CACHE_TTL = 900;

    private function getCacheVersion(string $scope): int
    {
        return (int) Cache::rememberForever("cache_version:infaq_mwc_dist:{$scope}", fn() => 1);
    }

    public function bumpCacheVersion(string $scope): void
    {
        $key = "cache_version:infaq_mwc_dist:{$scope}";

        if (!Cache::has($key)) {
            Cache::forever($key, 1);
            return;
        }

        Cache::increment($key);
    }

    public function getDistributions(
        ?int $wilayahId = null,
        ?string $jenisPilar = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): Collection {
        return InfaqMwcDistribution::query()
            ->with(['user', 'user.wilayah'])
            ->when($wilayahId, function ($q) use ($wilayahId) {
                $q->where(function ($q2) use ($wilayahId) {
                    $q2->where('wilayah_id', $wilayahId)
                       ->orWhereHas('user', function ($q3) use ($wilayahId) {
                           $q3->where('wilayah_id', $wilayahId);
                       });
                });
            })
            ->when($jenisPilar, fn($q) => $q->where('jenis_pilar', $jenisPilar))
            ->when($startDate, fn($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('date', '<=', $endDate))
            ->orderByDesc('date')
            ->get();
    }

    // ============================================================
    // CRUD METHODS
    // ============================================================

    public function findById(int $id): ?InfaqMwcDistribution
    {
        return InfaqMwcDistribution::find($id);
    }

    public function create(array $data): InfaqMwcDistribution
    {
        $distribution = InfaqMwcDistribution::create($data);
        
        if (isset($data['wilayah_id'])) {
            $this->bumpCacheVersion("wilayah:{$data['wilayah_id']}");
        }

        return $distribution;
    }

    public function update(int $id, array $data): bool
    {
        $distribution = $this->findById($id);
        if (!$distribution) {
            return false;
        }

        $updated = $distribution->update($data);

        if ($updated && $distribution->wilayah_id) {
            $this->bumpCacheVersion("wilayah:{$distribution->wilayah_id}");
        }

        return $updated;
    }

    public function delete(int $id): bool
    {
        $distribution = $this->findById($id);
        if (!$distribution) {
            return false;
        }

        $wilayahId = $distribution->wilayah_id;
        $deleted = $distribution->delete();

        if ($deleted && $wilayahId) {
            $this->bumpCacheVersion("wilayah:{$wilayahId}");
        }

        return $deleted;
    }

    // ============================================================
    // Get Count Data Infaq MWC
    // ============================================================

    public function getTotalPengeluaran(int $wilayahId, ?string $startDate = null, ?string $endDate = null): float
    {
        return InfaqMwcDistribution::where('wilayah_id', $wilayahId)
            ->when($startDate, fn($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('date', '<=', $endDate))
            ->sum('jumlah_total_distribusi');
    } 
}
