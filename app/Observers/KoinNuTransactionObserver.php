<?php

namespace App\Observers;

use App\Models\KoinNuTransaction;
use App\Repositories\KoinNuTransactionRepository;

class KoinNuTransactionObserver
{
    public function __construct(
        private readonly KoinNuTransactionRepository $repo
    ) {
    }

    public function saved(KoinNuTransaction $transaction): void
    {
        if (!$this->shouldInvalidate($transaction)) {
            return;
        }

        $this->invalidateScopes($transaction);
    }

    public function deleted(KoinNuTransaction $transaction): void
    {
        $this->invalidateScopes($transaction);
    }

    private function shouldInvalidate(KoinNuTransaction $transaction): bool
    {
        if ($transaction->wasRecentlyCreated) {
            return true;
        }

        return $transaction->wasChanged([
            'status',
            'date',
            'ranting_id',
            'wilayah_id',
            'pemasukan_koin_nu_bersih',
            'koin_nu_ranting',
            'koin_nu_mwc',
            'koin_nu_pc',
            'dana_dapat_digunakan_ranting',
            'dana_dapat_digunakan_mwc',
            'dana_dapat_digunakan_pc',
            'hak_amil_ranting',
            'hak_amil_mwc',
            'hak_amil_pc',
        ]);
    }

    private function invalidateScopes(KoinNuTransaction $transaction): void
    {
        if ($transaction->ranting_id) {
            $this->repo->bumpCacheVersion("ranting:{$transaction->ranting_id}");
        }

        if ($transaction->wilayah_id) {
            $this->repo->bumpCacheVersion("wilayah:{$transaction->wilayah_id}");
        }

        $this->repo->bumpCacheVersion('pc');
    }
}
