<?php

namespace App\Services;

use App\Models\KoinNuDistribution;
use App\Models\InfaqMwcDistribution;
use App\Models\InfaqPcDistribution;
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
        $isPc = $filters['is_pc'] ?? false;
        $sourceType = $filters['source_type'] ?? 'semua'; // semua, koin_nu, infaq_lainnya

        $data = collect();

        // 1. Fetch from KoinNuDistribution
        if ($sourceType === 'semua' || $sourceType === 'koin_nu') {
            $queryDist = KoinNuDistribution::with('wilayah');

            if ($startDate) $queryDist->where('date', '>=', $startDate);
            if ($endDate) $queryDist->where('date', '<=', $endDate);
            if ($wilayahId && !$isPc) {
                $queryDist->where('wilayah_id', $wilayahId);
            }

            $distributions = $queryDist->get()->map(function ($item) use ($isPc) {
                $amount = $isPc ? $item->jumlah_pentasarufan_pc : $item->jumlah_pentasarufan_mwc;
                $penerima = $isPc ? $item->jumlah_penerima_manfaat_pc : $item->jumlah_penerima_manfaat_mwc;

                // Hanya ambil yang ada nilainya di level ini
                if ($amount > 0) {
                    return [
                        'date' => $item->date,
                        'transaction_code' => $item->distribution_code,
                        'penerima_manfaat' => $penerima,
                        'event_name' => $item->deskripsi,
                        'amount' => $amount,
                        'type' => 'Koin NU',
                        'wilayah' => $item->wilayah->nama_wilayah ?? '-',
                        'status' => $item->status,
                    ];
                }
                return null;
            })->filter();

            $data = $data->concat($distributions);
        }

        // 2. Fetch from Infaq Distribution
        if ($sourceType === 'semua' || $sourceType === 'infaq_lainnya') {
            if ($isPc) {
                $query = InfaqPcDistribution::query();
                if ($startDate) $query->where('date', '>=', $startDate);
                if ($endDate) $query->where('date', '<=', $endDate);

                $infaqDistributions = $query->get()->map(function ($item) {
                    return [
                        'date' => $item->date,
                        'transaction_code' => $item->distribution_code,
                        'penerima_manfaat' => $item->jumlah_penerima_manfaat,
                        'event_name' => $item->deskripsi ?: '-',
                        'amount' => $item->jumlah_total_distribusi,
                        'type' => $item->jenis_pilar,
                        'wilayah' => 'PC',
                        'status' => 'Selesai',
                    ];
                });
            } else {
                $query = InfaqMwcDistribution::with('wilayah');
                if ($startDate) $query->where('date', '>=', $startDate);
                if ($endDate) $query->where('date', '<=', $endDate);
                if ($wilayahId) $query->where('wilayah_id', $wilayahId);

                $infaqDistributions = $query->get()->map(function ($item) {
                    return [
                        'date' => $item->date,
                        'transaction_code' => $item->distribution_code,
                        'penerima_manfaat' => $item->jumlah_penerima_manfaat,
                        'event_name' => $item->deskripsi ?: '-',
                        'amount' => $item->jumlah_total_distribusi,
                        'type' => $item->jenis_pilar,
                        'wilayah' => $item->wilayah->nama_wilayah ?? '-',
                        'status' => 'Selesai',
                    ];
                });
            }

            $data = $data->concat($infaqDistributions);
        }

        return $data->sortByDesc('date')->values();
    }
}
