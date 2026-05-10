<?php

namespace App\Services;

use App\Repositories\KoinNuDistributionRepository;
use App\Models\KoinNuDistribution;
use App\Models\KoinNuTransaction;
use Illuminate\Support\Facades\Auth;

class KoinNuDistributionService
{
    protected KoinNuDistributionRepository $repository;

    public function __construct(KoinNuDistributionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getBalance(int $rantingId): int
    {
        // Total dana dapat digunakan ranting dari transaksi yang approved
        $totalAllowed = KoinNuTransaction::where('ranting_id', $rantingId)
            ->where('status', 'approved')
            ->sum('dana_dapat_digunakan_ranting');

        // Total dana yang sudah didistribusikan (pentasarufan ranting)
        // Kita juga bisa mengecek yang approved atau semua
        $totalSpent = KoinNuDistribution::where('ranting_id', $rantingId)
            ->sum('jumlah_pentasarufan_ranting');

        return max($totalAllowed - $totalSpent, 0);
    }

    public function createDistribution(array $data, int $rantingId): KoinNuDistribution
    {
        $costAmount = $data['jumlah_pentasarufan_ranting'] ?? 0;
        
        $balance = $this->getBalance($rantingId);
        if ($costAmount > $balance) {
            throw new \Exception('Saldo tidak mencukupi. Sisa saldo yang dapat digunakan: Rp ' . number_format($balance, 0, ',', '.'));
        }

        $data['distribution_code'] = $this->generateDistributionCode();
        $data['user_id'] = Auth::id();
        $data['ranting_id'] = $rantingId;
        $data['wilayah_id'] = Auth::user()->wilayah_id ?? null;
        $data['status'] = 'pending';

        return $this->repository->create($data);
    }

    public function updateDistribution(int $id, array $data, int $rantingId): bool
    {
        $costAmount = $data['jumlah_pentasarufan_ranting'] ?? 0;

        $totalAllowed = KoinNuTransaction::where('ranting_id', $rantingId)
            ->where('status', 'approved')
            ->sum('dana_dapat_digunakan_ranting');

        $totalSpentExcludingMe = KoinNuDistribution::where('ranting_id', $rantingId)
            ->where('id', '!=', $id)
            ->sum('jumlah_pentasarufan_ranting');

        $balanceExcludingMe = max($totalAllowed - $totalSpentExcludingMe, 0);

        if ($costAmount > $balanceExcludingMe) {
            throw new \Exception('Saldo tidak mencukupi. Sisa saldo yang dapat digunakan: Rp ' . number_format($balanceExcludingMe, 0, ',', '.'));
        }

        return $this->repository->update($id, $data);
    }

    public function deleteDistribution(int $id): bool
    {
        return $this->repository->delete($id);
    }

    private function generateDistributionCode(): string
    {
        $last = KoinNuDistribution::where('distribution_code', 'like', 'DST%')->orderByDesc('id')->first();

        if (!$last || !$last->distribution_code) {
            return 'DST00001';
        }

        preg_match('/DST(\d+)/', $last->distribution_code, $matches);
        $lastNumber = isset($matches[1]) ? (int) $matches[1] : 0;
        $nextNumber = $lastNumber + 1;

        $digitLength = max(5, strlen((string) $nextNumber));
        return 'DST' . str_pad($nextNumber, $digitLength, '0', STR_PAD_LEFT);
    }
}
