<?php

namespace App\Services;

use App\Repositories\KoinNuTransactionRepository;
use App\Models\KoinNuTransaction;
use Illuminate\Support\Facades\Auth;

class KoinNuTransactionService
{
    protected KoinNuTransactionRepository $repository;

    public function __construct(KoinNuTransactionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createTransaction(array $data): KoinNuTransaction
    {
        $calculatedData = $this->calculateFields($data['pemasukan_koin_nu_kotor'], $data['jasa_petugas']);
        
        $transactionData = array_merge($data, $calculatedData);
        $transactionData['transaction_code'] = $this->generateTransactionCode();
        $transactionData['user_id'] = Auth::id();
        
        // Asumsi data ranting_id dan wilayah_id sudah ada di $data jika dari controller ranting/mwc
        if (Auth::user()->isRanting()) {
             $transactionData['ranting_id'] = Auth::user()->ranting_id ?? null;
             $transactionData['wilayah_id'] = Auth::user()->wilayah_id ?? null;
        }

        return $this->repository->create($transactionData);
    }

    public function updateTransaction(int $id, array $data): bool
    {
        if (isset($data['pemasukan_koin_nu_kotor']) && isset($data['jasa_petugas'])) {
            $calculatedData = $this->calculateFields($data['pemasukan_koin_nu_kotor'], $data['jasa_petugas']);
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

        // Alokasi per tingkat
        $koinNuRanting = (int) round($bersih * 0.60);
        $koinNuMwc = (int) round($bersih * 0.35);
        $koinNuPc = (int) round($bersih * 0.05);

        // Hak Amil (20% dari alokasi masing-masing)
        $hakAmilRanting = (int) round($koinNuRanting * 0.20);
        $hakAmilMwc = (int) round($koinNuMwc * 0.20);
        $hakAmilPc = (int) round($koinNuPc * 0.20);

        // Dana Dapat Digunakan
        $danaDigunakanRanting = $koinNuRanting - $hakAmilRanting;
        $danaDigunakanMwc = $koinNuMwc - $hakAmilMwc;
        $danaDigunakanPc = $koinNuPc - $hakAmilPc;

        return [
            'pemasukan_koin_nu_bersih' => $bersih,
            
            'koin_nu_ranting' => $koinNuRanting,
            'koin_nu_mwc' => $koinNuMwc,
            'koin_nu_pc' => $koinNuPc,

            'hak_amil_ranting' => $hakAmilRanting,
            'hak_amil_mwc' => $hakAmilMwc,
            'hak_amil_pc' => $hakAmilPc,

            'dana_dapat_digunakan_ranting' => $danaDigunakanRanting,
            'dana_dapat_digunakan_mwc' => $danaDigunakanMwc,
            'dana_dapat_digunakan_pc' => $danaDigunakanPc,
        ];
    }

    private function generateTransactionCode(): string
    {
        $last = KoinNuTransaction::where('transaction_code', 'like', 'KNU%')->orderByDesc('id')->first();

        if (!$last || !$last->transaction_code) {
            return 'KNU00001';
        }

        preg_match('/KNU(\d+)/', $last->transaction_code, $matches);
        $lastNumber = isset($matches[1]) ? (int) $matches[1] : 0;
        $nextNumber = $lastNumber + 1;

        $digitLength = max(5, strlen((string) $nextNumber));
        return 'KNU' . str_pad($nextNumber, $digitLength, '0', STR_PAD_LEFT);
    }
}
