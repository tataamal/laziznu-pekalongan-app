<?php

namespace App\Repositories;

use App\Models\KoinNuDistribution;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class KoinNuDistributionRepository
{
    private const CACHE_TTL = 900;

    private array $commonColumns = [
        'id',
        'user_id',
        'distribution_code',
        'date',
        'jenis_pilar',
        'deskripsi',
        'file_dokumentasi',
        'status'
    ];

    private function approvedQuery(?string $startDate = null, ?string $endDate = null): Builder
    {
        return KoinNuDistribution::query()
            ->where('status', 'approved')
            ->when($startDate, fn($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('date', '<=', $endDate));
    }

    private function getCacheVersion(string $scope): int
    {
        return (int) Cache::rememberForever("cache_version:dist:{$scope}", fn() => 1);
    }

    public function bumpCacheVersion(string $scope): void
    {
        $key = "cache_version:dist:{$scope}";

        if (!Cache::has($key)) {
            Cache::forever($key, 1);
            return;
        }
        Cache::increment($key);
    }

    public function getDistributionsRanting(
        int $rantingId,
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $jenisPilar = null,
        ?string $status = null
    ): Collection {
        return KoinNuDistribution::query()
            ->select([...$this->commonColumns, 'jumlah_pentasarufan_ranting', 'jumlah_penerima_manfaat_ranting'])
            ->where('ranting_id', $rantingId)
            ->when($jenisPilar, fn($q) => $q->where('jenis_pilar', $jenisPilar))
            ->when($startDate, fn($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('date', '<=', $endDate))
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderByDesc('date')
            ->get();
    }

    public function getDistributionsMwc(
        int $wilayahId,
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $jenisPilar = null
    ): Collection {
        return $this->approvedQuery($startDate, $endDate)
            ->select([...$this->commonColumns, 'jumlah_pentasarufan_mwc', 'jumlah_penerima_manfaat_mwc'])
            ->where('wilayah_id', $wilayahId)
            ->when($jenisPilar, fn($q) => $q->where('jenis_pilar', $jenisPilar))
            ->orderByDesc('date')
            ->get();
    }

    public function getAllDistributionsMwc(
        int $wilayahId,
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $jenisPilar = null
    ): Collection {
        return KoinNuDistribution::query()
            ->select([...$this->commonColumns, 'jumlah_pentasarufan_mwc', 'jumlah_penerima_manfaat_mwc'])
            ->where('wilayah_id', $wilayahId)
            ->when($jenisPilar, fn($q) => $q->where('jenis_pilar', $jenisPilar))
            ->when($startDate, fn($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('date', '<=', $endDate))
            ->orderByDesc('date')
            ->get();
    }

    public function getDistributionsPc(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $jenisPilar = null
    ): Collection {
        return $this->approvedQuery($startDate, $endDate)
            ->select([...$this->commonColumns, 'jumlah_pentasarufan_pc', 'jumlah_penerima_manfaat_pc'])
            ->when($jenisPilar, fn($q) => $q->where('jenis_pilar', $jenisPilar))
            ->orderByDesc('date')
            ->get();
    }

    // ===== SUMMARY METHODS (dengan cache) =====

    public function getSummaryRanting(
        int $rantingId,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $version = $this->getCacheVersion("ranting:{$rantingId}");
        $key = "summary:dist:ranting:v{$version}:{$rantingId}:{$startDate}:{$endDate}";

        return Cache::remember($key, self::CACHE_TTL, function () use ($rantingId, $startDate, $endDate) {
            $result = $this->approvedQuery($startDate, $endDate)
                ->where('ranting_id', $rantingId)
                ->selectRaw('
                    COUNT(*) as total_distribusi,
                    COALESCE(SUM(jumlah_pentasarufan), 0) as total_pentasarufan,
                    COALESCE(SUM(jumlah_penerima_manfaat), 0) as total_penerima
                ')
                ->first();

            return $result ? (array) $result : [];
        });
    }

    public function getSummaryMwc(
        int $wilayahId,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $version = $this->getCacheVersion("wilayah:{$wilayahId}");
        $key = "summary:dist:mwc:v{$version}:{$wilayahId}:{$startDate}:{$endDate}";

        return Cache::remember($key, self::CACHE_TTL, function () use ($wilayahId, $startDate, $endDate) {
            $result = $this->approvedQuery($startDate, $endDate)
                ->where('wilayah_id', $wilayahId)
                ->selectRaw('
                    COUNT(*) as total_distribusi,
                    COALESCE(SUM(jumlah_pentasarufan), 0) as total_pentasarufan,
                    COALESCE(SUM(jumlah_penerima_manfaat), 0) as total_penerima
                ')
                ->first();

            return $result ? (array) $result : [];
        });
    }

    public function getSummaryPc(
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $version = $this->getCacheVersion('pc');
        $key = "summary:dist:pc:v{$version}:{$startDate}:{$endDate}";

        return Cache::remember($key, self::CACHE_TTL, function () use ($startDate, $endDate) {
            $result = $this->approvedQuery($startDate, $endDate)
                ->selectRaw('
                    COUNT(*) as total_distribusi,
                    COALESCE(SUM(jumlah_pentasarufan), 0) as total_pentasarufan,
                    COALESCE(SUM(jumlah_penerima_manfaat), 0) as total_penerima
                ')
                ->first();

            return $result ? (array) $result : [];
        });
    }

    // ============================================================
    // CRUD METHODS
    // ============================================================

    public function findById(int $id): ?KoinNuDistribution
    {
        return KoinNuDistribution::find($id);
    }

    public function create(array $data): KoinNuDistribution
    {
        $distribution = KoinNuDistribution::create($data);
        
        if (isset($data['ranting_id'])) {
            $this->bumpCacheVersion("ranting:{$data['ranting_id']}");
        }
        if (isset($data['wilayah_id'])) {
            $this->bumpCacheVersion("wilayah:{$data['wilayah_id']}");
        }
        $this->bumpCacheVersion('pc');

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
            if ($distribution->ranting_id) {
                $this->bumpCacheVersion("ranting:{$distribution->ranting_id}");
            }
            if ($distribution->wilayah_id) {
                $this->bumpCacheVersion("wilayah:{$distribution->wilayah_id}");
            }
            $this->bumpCacheVersion('pc');
        }

        return $updated;
    }

    public function delete(int $id): bool
    {
        $distribution = $this->findById($id);
        if (!$distribution) {
            return false;
        }

        $rantingId = $distribution->ranting_id;
        $wilayahId = $distribution->wilayah_id;

        $deleted = $distribution->delete();

        if ($deleted) {
            if ($rantingId) {
                $this->bumpCacheVersion("ranting:{$rantingId}");
            }
            if ($wilayahId) {
                $this->bumpCacheVersion("wilayah:{$wilayahId}");
            }
            $this->bumpCacheVersion('pc');
        }

        return $deleted;
    }

    // ============================================================
    // GET COUNT PENDING DATA METHODS
    // ============================================================

    public function getCountPending(int $wilayahId)
    {
        return KoinNuDistribution::where('wilayah_id', $wilayahId)->where('status', 'pending')->count();
    }

    
}