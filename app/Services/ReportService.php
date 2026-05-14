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
        $isRanting = $filters['is_ranting'] ?? false;
        $sourceType = $filters['source_type'] ?? 'semua'; // semua, koin_nu, infaq_lainnya

        $data = collect();

        // 1. Fetch from KoinNuDistribution
        if ($sourceType === 'semua' || $sourceType === 'koin_nu') {
            $queryDist = KoinNuDistribution::with(['wilayah', 'ranting']);

            if ($startDate) $queryDist->where('date', '>=', $startDate);
            if ($endDate) $queryDist->where('date', '<=', $endDate);
            if ($wilayahId && !$isPc) {
                $queryDist->where('wilayah_id', $wilayahId);
            }

            if (!$isPc && !$isRanting) {
                $queryDist->where('jumlah_pentasarufan_mwc', '>', 0);
            } elseif ($isPc && !$isRanting) {
                $queryDist->where('jumlah_pentasarufan_pc', '>', 0);
            } elseif ($isRanting) {
                $queryDist->where('jumlah_pentasarufan_ranting', '>', 0);
            }

            $distributions = $queryDist->get()->map(function ($item) use ($isPc, $isRanting) {
                if ($isRanting) {
                    $amount = $item->jumlah_pentasarufan_ranting;
                    $penerima = $item->jumlah_penerima_manfaat_ranting;
                } elseif ($isPc) {
                    $amount = $item->jumlah_pentasarufan_pc;
                    $penerima = $item->jumlah_penerima_manfaat_pc;
                } else {
                    $amount = $item->jumlah_pentasarufan_mwc;
                    $penerima = $item->jumlah_penerima_manfaat_mwc;
                }

                // Hanya ambil yang ada nilainya di level ini
                if ($amount > 0) {
                    return [
                        'distribution_code' => $item->distribution_code ?? '-',
                        'date' => $item->date,
                        'jenis_pilar' => $item->jenis_pilar ?? 'Koin NU',
                        'deskripsi' => $item->deskripsi,
                        'jumlah_penerima_manfaat' => $penerima,
                        'keterangan' => $item->deskripsi,
                        'jumlah_total_distribusi' => $amount,
                        'nama_wilayah' => $item->wilayah->nama_wilayah ?? '-',
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
                        'distribution_code' => $item->distribution_code,
                        'date' => $item->date,
                        'jenis_pilar' => $item->jenis_pilar,
                        'deskripsi' => $item->deskripsi,
                        'jumlah_penerima_manfaat' => $item->jumlah_penerima_manfaat,
                        'keterangan' => $item->keterangan ?? '-',
                        'jumlah_total_distribusi' => $item->jumlah_total_distribusi,
                        'nama_wilayah' => $item->wilayah->nama_wilayah ?? '-',
                    ];
                });
            }

            $data = $data->concat($infaqDistributions);
        }

        return $data->sortByDesc('date')->values();
    }
}
