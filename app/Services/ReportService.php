<?php

namespace App\Services;

use App\Models\Distribution;
use App\Models\InfaqTransaction;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Get pentasarufan data from multiple sources.
     *
     * @param array $filters
     * @return Collection
     */
    public function getPentasarufanData(array $filters): Collection
    {
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;
        $wilayahId = $filters['wilayah_id'] ?? null;
        $sourceType = $filters['source_type'] ?? 'semua'; // semua, koin_nu, infaq_lainnya

        $data = collect();

        // 1. Fetch from Distribution (Koin NU Events) dan Infaq Koin NU
        if ($sourceType === 'semua' || $sourceType === 'koin_nu') {
            $queryDist = Distribution::with('user.wilayah');

            if ($startDate) $queryDist->where('date', '>=', $startDate);
            if ($endDate) $queryDist->where('date', '<=', $endDate);
            if ($wilayahId) {
                $queryDist->whereHas('user', function ($q) use ($wilayahId) {
                    $q->where('wilayah_id', $wilayahId);
                });
            }

            $distributions = $queryDist->get()->map(function ($item) {
                return [
                    'date' => $item->date,
                    'transaction_code' => $item->transaction_code,
                    'penerima_manfaat' => $item->penerima_manfaat,
                    'event_name' => $item->event_name,
                    'amount' => $item->cost_amount,
                    'type' => 'Koin NU',
                    'wilayah' => $item->user->wilayah->nama_wilayah ?? '-',
                    'status' => $item->status,
                ];
            });

            // Tambahkan Pengeluaran InfaqTransaction yg tipenya Saldo Koin NU
            $queryInfaqKoin = InfaqTransaction::with('user.wilayah')
                ->where('transaction_type', 'Pengeluaran')
                ->where('infaq_type', 'Saldo Koin NU');

            if ($startDate) $queryInfaqKoin->where('transaction_date', '>=', $startDate);
            if ($endDate) $queryInfaqKoin->where('transaction_date', '<=', $endDate);
            if ($wilayahId) {
                $queryInfaqKoin->whereHas('user', function ($q) use ($wilayahId) {
                    $q->where('wilayah_id', $wilayahId);
                });
            }

            $infaqKoinNU = $queryInfaqKoin->get()->map(function ($item) {
                return [
                    'date' => $item->transaction_date,
                    'transaction_code' => $item->transaction_code,
                    'penerima_manfaat' => $item->penerima_manfaat,
                    'event_name' => $item->description ?: '-',
                    'amount' => $item->gross_amount,
                    'type' => $item->infaq_type,
                    'wilayah' => $item->user->wilayah->nama_wilayah ?? '-',
                    'status' => 'Selesai',
                ];
            });

            $data = $data->concat($distributions)->concat($infaqKoinNU);
        }

        // 2. Fetch from InfaqTransaction (Infaq Lainnya)
        if ($sourceType === 'semua' || $sourceType === 'infaq_lainnya') {
            $query = InfaqTransaction::with('user.wilayah')
                ->where('transaction_type', 'Pengeluaran')
                ->where('infaq_type', '!=', 'Saldo Koin NU');

            if ($startDate) {
                $query->where('transaction_date', '>=', $startDate);
            }
            if ($endDate) {
                $query->where('transaction_date', '<=', $endDate);
            }
            if ($wilayahId) {
                $query->whereHas('user', function ($q) use ($wilayahId) {
                    $q->where('wilayah_id', $wilayahId);
                });
            }

            $infaqTransactions = $query->get()->map(function ($item) {
                return [
                    'date' => $item->transaction_date,
                    'transaction_code' => $item->transaction_code,
                    'penerima_manfaat' => $item->penerima_manfaat,
                    'event_name' => $item->description ?: '-',
                    'amount' => $item->gross_amount,
                    'type' => $item->infaq_type,
                    'wilayah' => $item->user->wilayah->nama_wilayah ?? '-',
                    'status' => 'Selesai',
                ];
            });

            $data = $data->concat($infaqTransactions);
        }

        return $data->sortByDesc('date')->values();
    }
}
