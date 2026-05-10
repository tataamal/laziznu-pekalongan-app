<?php

namespace App\Services;

use App\Repositories\InfaqPcTransactionRepository;
use App\Models\InfaqPcTransaction;
use Illuminate\Support\Facades\Auth;

class InfaqPcTransactionService
{
    protected InfaqPcTransactionRepository $repository;

    public function __construct(InfaqPcTransactionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createTransaction(array $data): InfaqPcTransaction
    {
        $calculatedData = $this->calculateFields($data['pemasukan_infaq_kotor'], $data['jasa_petugas'] ?? 0);
        
        $transactionData = array_merge($data, $calculatedData);
        $transactionData['transaction_code'] = $this->generateTransactionCode();
        $transactionData['user_id'] = Auth::id();

        return $this->repository->create($transactionData);
    }

    public function updateTransaction(int $id, array $data): bool
    {
        if (isset($data['pemasukan_infaq_kotor'])) {
            $jasaPetugas = $data['jasa_petugas'] ?? 0;
            $calculatedData = $this->calculateFields($data['pemasukan_infaq_kotor'], $jasaPetugas);
            $data = array_merge($data, $calculatedData);
        }

        return $this->repository->update($id, $data);
    }

    public function deleteTransaction(int $id): bool
    {
        return $this->repository->delete($id);
    }

    private function calculateFields(int $kotor, int $jasaPetugas): array
    {
        $bersih = max($kotor - $jasaPetugas, 0);

        // Amil 10%
        $hakAmil = (int) round($bersih * 0.10);

        // Dana Dapat Digunakan
        $danaDigunakan = $bersih - $hakAmil;

        return [
            'pemasukan_infaq_bersih' => $bersih,
            'hak_amil' => $hakAmil,
            'infaq_yang_dapat_digunakan' => $danaDigunakan,
        ];
    }

    private function generateTransactionCode(): string
    {
        $last = InfaqPcTransaction::where('transaction_code', 'like', 'INPC%')->orderByDesc('id')->first();

        if (!$last || !$last->transaction_code) {
            return 'INPC00001';
        }

        preg_match('/INPC(\d+)/', $last->transaction_code, $matches);
        $lastNumber = isset($matches[1]) ? (int) $matches[1] : 0;
        $nextNumber = $lastNumber + 1;

        $digitLength = max(5, strlen((string) $nextNumber));
        return 'INPC' . str_pad($nextNumber, $digitLength, '0', STR_PAD_LEFT);
    }
}
