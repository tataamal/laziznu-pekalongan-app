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

        // 1. Fetch from Distribution (Koin NU)
        if ($sourceType === 'semua' || $sourceType === 'koin_nu') {
            $query = Distribution::with('user.wilayah');

            if ($startDate) {
                $query->where('date', '>=', $startDate);
            }
            if ($endDate) {
                $query->where('date', '<=', $endDate);
            }
            if ($wilayahId) {
                $query->whereHas('user', function ($q) use ($wilayahId) {
                    $q->where('wilayah_id', $wilayahId);
                });
            }

            $distributions = $query->get()->map(function ($item) {
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

            $data = $data->concat($distributions);
        }

        // 2. Fetch from InfaqTransaction (Infaq Lainnya)
        if ($sourceType === 'semua' || $sourceType === 'infaq_lainnya') {
            $query = InfaqTransaction::with('user.wilayah')
                ->where('transaction_type', 'Pengeluaran');

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
                    'event_name' => $item->infaq_type . ' - ' . $item->description,
                    'amount' => $item->gross_amount,
                    'type' => 'Infaq Lainnya',
                    'wilayah' => $item->user->wilayah->nama_wilayah ?? '-',
                    'status' => 'Selesai', // Infaq transactions are usually immediate
                ];
            });

            $data = $data->concat($infaqTransactions);
        }

        return $data->sortByDesc('date')->values();
    }
}
