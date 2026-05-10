<?php

namespace App\Services;

use App\Repositories\InfaqMwcDistributionRepository;
use App\Models\InfaqMwcDistribution;
use App\Models\InfaqMwcTransaction;
use Illuminate\Support\Facades\Auth;

class InfaqMwcDistributionService
{
    protected InfaqMwcDistributionRepository $repository;

    public function __construct(InfaqMwcDistributionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getBalance(int $wilayahId): int
    {
        $totalAllowed = InfaqMwcTransaction::where('wilayah_id', $wilayahId)
            ->sum('infaq_yang_dapat_digunakan');

        $totalSpent = InfaqMwcDistribution::where('wilayah_id', $wilayahId)
            ->sum('jumlah_total_distribusi');

        return max($totalAllowed - $totalSpent, 0);
    }

    public function createDistribution(array $data, int $wilayahId): InfaqMwcDistribution
    {
        $costAmount = $data['jumlah_total_distribusi'] ?? 0;
        
        $balance = $this->getBalance($wilayahId);
        if ($costAmount > $balance) {
            throw new \Exception('Saldo Infaq MWC tidak mencukupi. Sisa saldo yang dapat digunakan: Rp ' . number_format($balance, 0, ',', '.'));
        }

        $data['distribution_code'] = $this->generateDistributionCode();
        $data['user_id'] = Auth::id();
        $data['wilayah_id'] = $wilayahId;

        return $this->repository->create($data);
    }

    public function updateDistribution(int $id, array $data, int $wilayahId): bool
    {
        $costAmount = $data['jumlah_total_distribusi'] ?? 0;

        $totalAllowed = InfaqMwcTransaction::where('wilayah_id', $wilayahId)
            ->sum('infaq_yang_dapat_digunakan');

        $totalSpentExcludingMe = InfaqMwcDistribution::where('wilayah_id', $wilayahId)
            ->where('id', '!=', $id)
            ->sum('jumlah_total_distribusi');

        $balanceExcludingMe = max($totalAllowed - $totalSpentExcludingMe, 0);

        if ($costAmount > $balanceExcludingMe) {
            throw new \Exception('Saldo Infaq MWC tidak mencukupi. Sisa saldo yang dapat digunakan: Rp ' . number_format($balanceExcludingMe, 0, ',', '.'));
        }

        return $this->repository->update($id, $data);
    }

    public function deleteDistribution(int $id): bool
    {
        return $this->repository->delete($id);
    }

    private function generateDistributionCode(): string
    {
        $last = InfaqMwcDistribution::where('distribution_code', 'like', 'DSM%')->orderByDesc('id')->first();

        if (!$last || !$last->distribution_code) {
            return 'DSM00001';
        }

        preg_match('/DSM(\d+)/', $last->distribution_code, $matches);
        $lastNumber = isset($matches[1]) ? (int) $matches[1] : 0;
        $nextNumber = $lastNumber + 1;

        $digitLength = max(5, strlen((string) $nextNumber));
        return 'DSM' . str_pad($nextNumber, $digitLength, '0', STR_PAD_LEFT);
    }
}
