<?php

namespace App\Repositories;

use App\Models\KoinNuTransaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class KoinNuTransactionRepository
{
    private const CACHE_TTL = 900; // 15 menit

    private array $commonColumns = [
        'id',
        'user_id',
        'transaction_code',
        'date',
        'jumlah_kaleng',
        'pemasukan_koin_nu_kotor',
        'jasa_petugas',
        'pemasukan_koin_nu_bersih',
        'status',
    ];

    // ============================================================
    // PRIVATE HELPERS
    // ============================================================

    /**
     * Base query untuk transaksi approved dengan filter periode.
     * Dipakai oleh hampir semua method untuk konsistensi & DRY.
     */
    private function approvedQuery(?string $startDate = null, ?string $endDate = null): Builder
    {
        return KoinNuTransaction::query()
            ->where('status', 'approved')
            ->when($startDate, fn($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('date', '<=', $endDate));
    }

    /**
     * Ambil versi cache untuk scope tertentu (untuk versioned cache key).
     */
    private function getCacheVersion(string $scope): int
    {
        return (int) Cache::rememberForever("cache_version:{$scope}", fn() => 1);
    }

    /**
     * Naikkan versi cache → invalidate semua cache untuk scope ini.
     */
    public function bumpCacheVersion(string $scope): void
    {
        $key = "cache_version:{$scope}";

        if (!Cache::has($key)) {
            Cache::forever($key, 1);
            return;
        }

        Cache::increment($key);
    }

    /**
     * Cleanup cache yang sudah expired (dipanggil oleh scheduled command).
     */
    public function pruneExpiredCache(): int
    {
        return DB::table('cache')
            ->where('expiration', '<', now()->timestamp)
            ->delete();
    }

    // ============================================================
    // LIST METHODS — untuk halaman listing detail (tidak di-cache)
    // ============================================================

    /**
     * Raw access ke transaksi (admin/audit). Tanpa filter status default.
     */
    public function getKoinNuTransactions(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $status = null
    ): Collection {
        return KoinNuTransaction::query()
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($startDate, fn($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('date', '<=', $endDate))
            ->orderByDesc('date')
            ->get();
    }

    /**
     * Daftar transaksi level ranting (untuk halaman list ranting).
     */
    public function getKoinNuRanting(
        int $rantingId,
        ?string $status = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): Collection {
        return KoinNuTransaction::query()
            ->select([
                ...$this->commonColumns,
                'koin_nu_ranting',
                'dana_dapat_digunakan_ranting',
                'hak_amil_ranting',
            ])
            ->where('ranting_id', $rantingId)
            ->when($status && $status !== 'all', fn($q) => $q->where('status', $status))
            ->when($startDate, fn($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('date', '<=', $endDate))
            ->orderByDesc('date')
            ->get();
    }

    /**
     * Daftar transaksi level MWC (untuk halaman list MWC).
     */
    public function getKoinNuMwc(
        int $wilayahId,
        ?string $startDate = null,
        ?string $endDate = null
    ): Collection {
        return $this->approvedQuery($startDate, $endDate)
            ->select([
                ...$this->commonColumns,
                'koin_nu_mwc',
                'dana_dapat_digunakan_mwc',
                'hak_amil_mwc',
            ])
            ->where('wilayah_id', $wilayahId)
            ->orderByDesc('date')
            ->get();
    }

    public function getRequestApprovalKoinNuMwc(
        int $wilayahId,
        ?string $startDate = null,
        ?string $endDate = null,
        array $filters = []
    ): Collection {
        return KoinNuTransaction::query()
            ->select([
                ...$this->commonColumns,
                'koin_nu_ranting',
                'koin_nu_mwc',
                'koin_nu_pc',
                'dana_dapat_digunakan_ranting',
                'dana_dapat_digunakan_mwc',
                'dana_dapat_digunakan_pc',
                'hak_amil_ranting',
                'hak_amil_mwc',
                'hak_amil_pc',
                'ranting_id',
            ])
            ->with('ranting')
            ->where('wilayah_id', $wilayahId)
            ->where('status', 'pending')
            ->when($startDate, fn($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('date', '<=', $endDate))
            ->when(!empty($filters['transaction_code']), fn($q) => $q->where('transaction_code', 'like', '%' . $filters['transaction_code'] . '%'))
            ->when(!empty($filters['ranting_name']), fn($q) => $q->whereHas('ranting', fn($q2) => $q2->where('nama_ranting', 'like', '%' . $filters['ranting_name'] . '%')))
            ->orderByDesc('date')
            ->get();
    }

    public function getHistoryKoinNuMwc(
        int $wilayahId,
        ?string $startDate = null,
        ?string $endDate = null,
        array $filters = []
    ): Collection {
        return KoinNuTransaction::query()
            ->select([
                ...$this->commonColumns,
                'koin_nu_ranting',
                'koin_nu_mwc',
                'koin_nu_pc',
                'dana_dapat_digunakan_ranting',
                'dana_dapat_digunakan_mwc',
                'dana_dapat_digunakan_pc',
                'hak_amil_ranting',
                'hak_amil_mwc',
                'hak_amil_pc',
                'ranting_id',
            ])
            ->where('wilayah_id', $wilayahId)
            ->with('ranting')
            ->whereIn('status', ['approved', 'rejected', 'validated'])
            ->when($startDate, fn($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('date', '<=', $endDate))
            ->when(!empty($filters['transaction_code']), fn($q) => $q->where('transaction_code', 'like', '%' . $filters['transaction_code'] . '%'))
            ->when(!empty($filters['ranting_name']), fn($q) => $q->whereHas('ranting', fn($q2) => $q2->where('nama_ranting', 'like', '%' . $filters['ranting_name'] . '%')))
            ->when(!empty($filters['status']) && $filters['status'] !== 'all', function ($q) use ($filters) {
                if ($filters['status'] === 'validated') {
                    $q->whereIn('status', ['validated', 'approved']);
                } else {
                    $q->where('status', $filters['status']);
                }
            })
            ->orderByDesc('date')
            ->get();
    }

    /**
     * Ambil data koin NU MWC yang di-group per ranting.
     */
    public function getKoinNuMwcGroupedByRanting(int $wilayahId): Collection
    {
        return $this->approvedQuery()
            ->selectRaw('ranting_id, SUM(koin_nu_mwc) as total_koin')
            ->where('wilayah_id', $wilayahId)
            ->groupBy('ranting_id')
            ->with('ranting:id,nama_ranting')
            ->get();
    }

    /**
     * Get Data Transaksi Koin NU PC.
     * @param $startDate
     * @param $endDate
     * @return Collection
     */
    public function getKoinNuPc(
        ?string $startDate = null,
        ?string $endDate = null
    ): Collection {
        return $this->approvedQuery($startDate, $endDate)
            ->select([
                ...$this->commonColumns,
                'koin_nu_pc',
                'dana_dapat_digunakan_pc',
                'hak_amil_pc',
            ])
            ->orderByDesc('date')
            ->get();
    }

    public function sumKoinNuPc(
        ?string $startDate = null,
        ?string $endDate = null
    ): int {
        return $this->approvedQuery($startDate, $endDate)
            ->sum('koin_nu_pc');
    }

    /**
     * Get Hak Amil PC
     * @param $startDate
     * @param $endDate
     * @return int
     */
    public function getHakAmilPc(
        ?string $startDate = null,
        ?string $endDate = null
    ): int {
        return $this->approvedQuery($startDate, $endDate)
            ->select([...$this->commonColumns, 'hak_amil_pc'])
            ->sum('hak_amil_pc');
    }

    /**
     * Get Data Dana yang dapat digunakan untuk PC
     * @param $startDate
     * @param $endDate
     * @return int
     */
    public function getDanaDapatDigunakanPc(
        ?string $startDate = null,
        ?string $endDate = null
    ): int {
        return $this->approvedQuery($startDate, $endDate)
            ->select([...$this->commonColumns, 'dana_dapat_digunakan_pc'])
            ->sum('dana_dapat_digunakan_pc');
    }
    // ============================================================
    // SUMMARY METHODS — untuk dashboard (di-cache, 1 query agregat)
    // ============================================================

    /**
     * Summary agregat level ranting untuk dashboard.
     */
    public function getSummaryRanting(
        int $rantingId,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $version = $this->getCacheVersion("ranting:{$rantingId}");
        $key = "summary:ranting:v{$version}:{$rantingId}:{$startDate}:{$endDate}";

        return Cache::remember($key, self::CACHE_TTL, function () use ($rantingId, $startDate, $endDate) {
            $result = $this->approvedQuery($startDate, $endDate)
                ->where('ranting_id', $rantingId)
                ->selectRaw('
                    COUNT(*) as total_transaksi,
                    COALESCE(SUM(jumlah_kaleng), 0) as total_kaleng,
                    COALESCE(SUM(pemasukan_koin_nu_kotor), 0) as total_pemasukan_kotor,
                    COALESCE(SUM(jasa_petugas), 0) as total_jasa_petugas,
                    COALESCE(SUM(pemasukan_koin_nu_bersih), 0) as total_pemasukan_bersih,
                    COALESCE(SUM(koin_nu_ranting), 0) as total_alokasi,
                    COALESCE(SUM(dana_dapat_digunakan_ranting), 0) as total_dana_digunakan,
                    COALESCE(SUM(hak_amil_ranting), 0) as total_hak_amil
                ')
                ->first();

            return $result ? (array) $result : [];
        });
    }

    /**
     * Summary agregat level MWC untuk dashboard.
     */
    public function getSummaryMwc(
        int $wilayahId,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $version = $this->getCacheVersion("wilayah:{$wilayahId}");
        $key = "summary:mwc:v{$version}:{$wilayahId}:{$startDate}:{$endDate}";

        return Cache::remember($key, self::CACHE_TTL, function () use ($wilayahId, $startDate, $endDate) {
            $result = $this->approvedQuery($startDate, $endDate)
                ->where('wilayah_id', $wilayahId)
                ->selectRaw('
                    COUNT(*) as total_transaksi,
                    COALESCE(SUM(jumlah_kaleng), 0) as total_kaleng,
                    COALESCE(SUM(pemasukan_koin_nu_kotor), 0) as total_pemasukan_kotor,
                    COALESCE(SUM(jasa_petugas), 0) as total_jasa_petugas,
                    COALESCE(SUM(pemasukan_koin_nu_bersih), 0) as total_pemasukan_bersih,
                    COALESCE(SUM(koin_nu_mwc), 0) as total_alokasi,
                    COALESCE(SUM(dana_dapat_digunakan_mwc), 0) as total_dana_digunakan,
                    COALESCE(SUM(hak_amil_mwc), 0) as total_hak_amil
                ')
                ->first();

            return $result ? (array) $result : [];
        });
    }

    /**
     * Summary agregat level PC untuk dashboard.
     */
    public function getSummaryPc(
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $version = $this->getCacheVersion('pc');
        $key = "summary:pc:v{$version}:{$startDate}:{$endDate}";

        return Cache::remember($key, self::CACHE_TTL, function () use ($startDate, $endDate) {
            $result = $this->approvedQuery($startDate, $endDate)
                ->selectRaw('
                    COUNT(*) as total_transaksi,
                    COALESCE(SUM(jumlah_kaleng), 0) as total_kaleng,
                    COALESCE(SUM(pemasukan_koin_nu_kotor), 0) as total_pemasukan_kotor,
                    COALESCE(SUM(jasa_petugas), 0) as total_jasa_petugas,
                    COALESCE(SUM(pemasukan_koin_nu_bersih), 0) as total_pemasukan_bersih,
                    COALESCE(SUM(koin_nu_pc), 0) as total_alokasi,
                    COALESCE(SUM(dana_dapat_digunakan_pc), 0) as total_dana_digunakan,
                    COALESCE(SUM(hak_amil_pc), 0) as total_hak_amil
                ')
                ->first();

            return $result ? (array) $result : [];
        });
    }

    // ============================================================
    // CRUD METHODS
    // ============================================================

    public function findById(int $id): ?KoinNuTransaction
    {
        return KoinNuTransaction::find($id);
    }

    public function create(array $data): KoinNuTransaction
    {
        $transaction = KoinNuTransaction::create($data);

        // Invalidate cache since new data is added
        if (isset($data['ranting_id'])) {
            $this->bumpCacheVersion("ranting:{$data['ranting_id']}");
        }
        if (isset($data['wilayah_id'])) {
            $this->bumpCacheVersion("wilayah:{$data['wilayah_id']}");
        }
        $this->bumpCacheVersion('pc');

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
            if ($transaction->ranting_id) {
                $this->bumpCacheVersion("ranting:{$transaction->ranting_id}");
            }
            if ($transaction->wilayah_id) {
                $this->bumpCacheVersion("wilayah:{$transaction->wilayah_id}");
            }
            $this->bumpCacheVersion('pc');
        }

        return $updated;
    }

    public function delete(int $id): bool
    {
        $transaction = $this->findById($id);
        if (!$transaction) {
            return false;
        }

        $rantingId = $transaction->ranting_id;
        $wilayahId = $transaction->wilayah_id;

        $deleted = $transaction->delete();

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
        return KoinNuTransaction::where('wilayah_id', $wilayahId)->where('status', 'pending')->count();
    }
}