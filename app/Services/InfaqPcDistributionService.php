<?php

namespace App\Services;

use App\Repositories\InfaqPcDistributionRepository;
use App\Models\InfaqPcDistribution;
use App\Models\InfaqPcTransaction;
use Illuminate\Support\Facades\Auth;

class InfaqPcDistributionService
{
    protected InfaqPcDistributionRepository $repository;

    public function __construct(InfaqPcDistributionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getBalance(): int
    {
        $totalAllowed = InfaqPcTransaction::sum('infaq_yang_dapat_digunakan');
        $totalSpent = InfaqPcDistribution::sum('jumlah_total_distribusi');

        return max($totalAllowed - $totalSpent, 0);
    }

    public function createDistribution(array $data): InfaqPcDistribution
    {
        $costAmount = $data['jumlah_total_distribusi'] ?? 0;
        
        $balance = $this->getBalance();
        if ($costAmount > $balance) {
            throw new \Exception('Saldo Infaq PC tidak mencukupi. Sisa saldo yang dapat digunakan: Rp ' . number_format($balance, 0, ',', '.'));
        }

        $data['distribution_code'] = $this->generateDistributionCode();
        $data['user_id'] = Auth::id();

        return $this->repository->create($data);
    }

    public function updateDistribution(int $id, array $data): bool
    {
        $costAmount = $data['jumlah_total_distribusi'] ?? 0;

        $totalAllowed = InfaqPcTransaction::sum('infaq_yang_dapat_digunakan');

        $totalSpentExcludingMe = InfaqPcDistribution::where('id', '!=', $id)
            ->sum('jumlah_total_distribusi');

        $balanceExcludingMe = max($totalAllowed - $totalSpentExcludingMe, 0);

        if ($costAmount > $balanceExcludingMe) {
            throw new \Exception('Saldo Infaq PC tidak mencukupi. Sisa saldo yang dapat digunakan: Rp ' . number_format($balanceExcludingMe, 0, ',', '.'));
        }

        return $this->repository->update($id, $data);
    }

    public function deleteDistribution(int $id): bool
    {
        return $this->repository->delete($id);
    }

    private function generateDistributionCode(): string
    {
        $last = InfaqPcDistribution::where('distribution_code', 'like', 'DSPC%')->orderByDesc('id')->first();

        if (!$last || !$last->distribution_code) {
            return 'DSPC00001';
        }

        preg_match('/DSPC(\d+)/', $last->distribution_code, $matches);
        $lastNumber = isset($matches[1]) ? (int) $matches[1] : 0;
        $nextNumber = $lastNumber + 1;

        $digitLength = max(5, strlen((string) $nextNumber));
        return 'DSPC' . str_pad($nextNumber, $digitLength, '0', STR_PAD_LEFT);
    }
}
