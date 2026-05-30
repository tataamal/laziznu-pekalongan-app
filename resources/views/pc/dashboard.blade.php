@extends('layouts.app')

@section('page_title', 'Dashboard PC')
@section('page_subtitle', 'Monitoring Cashfolow KOIN NU dan Infaq Kabupaten Pekalongan.')

@section('content')
    @push('vite-scripts')
        @vite('resources/js/pc-dashboard.js')
    @endpush

    <div class="w-full space-y-4">
        <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div id="card-koin-nu" class="relative rounded-3xl bg-gradient-to-br from-green-700 to-emerald-500 p-5 text-white shadow-sm transition-all duration-300 hover:shadow-lg cursor-pointer">
                <div class="text-sm font-medium text-white/90 flex items-center justify-between">
                    <span>Saldo Koin NU PC</span>
                    <i class="fa-solid fa-circle-info text-white/80"></i>
                </div>
                <div class="mt-4 text-3xl font-bold">Rp {{ number_format($total_koin_nu ?? 0, 0, ',', '.') }}</div>
                <div class="mt-3 text-xs text-white/80">Total pemasukan Koin NU untuk alokasi PC</div>
            </div>

            {{-- Tooltip Box --}}
            <div id="tooltip-koin-nu" class="fixed pointer-events-none opacity-0 invisible transition-all duration-200 z-50 text-xs w-64 bg-white/95 backdrop-blur-md text-zinc-900 rounded-2xl p-4 shadow-2xl border border-zinc-200/50">
                <div class="font-semibold mb-2 border-b border-zinc-100 pb-1 text-zinc-700">Detail per MWC</div>
                <div class="space-y-1.5 max-h-[150px] overflow-y-auto scrollbar-thin scrollbar-thumb-zinc-200">
                    @forelse($koinNuByMwc as $item)
                        <div class="flex justify-between items-center">
                            <span class="truncate pr-2 text-zinc-600">{{ $item->wilayah ? $item->wilayah->nama_wilayah : 'Transaksi PC' }}</span>
                            <span class="font-bold text-zinc-800">Rp {{ number_format($item->total_koin, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="text-zinc-400 text-center py-2">Belum ada data</div>
                    @endforelse
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const card = document.getElementById('card-koin-nu');
                    const tooltip = document.getElementById('tooltip-koin-nu');
                    
                    if (card && tooltip) {
                        card.addEventListener('mousemove', function(e) {
                            tooltip.style.left = (e.clientX + 15) + 'px';
                            tooltip.style.top = (e.clientY - 15) + 'px';
                            tooltip.classList.remove('opacity-0', 'invisible');
                            tooltip.classList.add('opacity-100', 'visible');
                        });
                        
                        card.addEventListener('mouseleave', function() {
                            tooltip.classList.add('opacity-0', 'invisible');
                            tooltip.classList.remove('opacity-100', 'visible');
                        });
                    }
                });
            </script>

            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-zinc-600">Pengeluaran Koin NU PC</div>
                <div class="mt-4 text-3xl font-bold tracking-tight text-zinc-900">Rp {{ number_format($total_koin_nu_distribusi ?? 0, 0, ',', '.') }}</div>
                <div class="mt-3 text-xs text-zinc-500">Total pentasarufan dari saldo Koin NU PC</div>
            </div>

            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-zinc-600">Hak Amil Koin NU PC</div>
                <div class="mt-4 text-3xl font-bold tracking-tight text-zinc-900">Rp {{ number_format($total_hak_amil_pc ?? 0, 0, ',', '.') }}</div>
                <div class="mt-3 text-xs text-zinc-500">Total hak amil dari pemasukan Koin NU PC</div>
            </div>

            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-zinc-600">Saldo Koin NU Dapat Digunakan</div>
                <div class="mt-4 text-3xl font-bold tracking-tight text-zinc-900">Rp {{ number_format($sisa_dana_koin_nu ?? 0, 0, ',', '.') }}</div>
                <div class="mt-3 text-xs text-zinc-500">Sisa dana setelah dikurangi distribusi Koin NU</div>
            </div>

            <div class="rounded-3xl bg-gradient-to-br from-yellow-700 to-amber-500 p-5 text-white shadow-sm">
                <div class="text-sm font-medium text-white/90">Saldo Infaq PC</div>
                <div class="mt-4 text-3xl font-bold">Rp {{ number_format($total_infaq_pc ?? 0, 0, ',', '.') }}</div>
                <div class="mt-3 text-xs text-white/80">Total pemasukan infaq bersih untuk PC</div>
            </div>

            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-zinc-600">Pengeluaran Infaq PC</div>
                <div class="mt-4 text-3xl font-bold tracking-tight text-zinc-900">Rp {{ number_format($total_infaq_pc_distribusi ?? 0, 0, ',', '.') }}</div>
                <div class="mt-3 text-xs text-zinc-500">Total distribusi dari saldo infaq PC</div>
            </div>

            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-zinc-600">Hak Amil Infaq PC</div>
                <div class="mt-4 text-3xl font-bold tracking-tight text-zinc-900">Rp {{ number_format($total_hak_amil_pc_infaq ?? 0, 0, ',', '.') }}</div>
                <div class="mt-3 text-xs text-zinc-500">Total hak amil dari transaksi infaq PC</div>
            </div>

            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-zinc-600">Infaq Dapat Digunakan PC</div>
                <div class="mt-4 text-3xl font-bold tracking-tight text-zinc-900">Rp {{ number_format($sisa_dana_infaq_pc ?? 0, 0, ',', '.') }}</div>
                <div class="mt-3 text-xs text-zinc-500">Sisa dana infaq setelah pengeluaran distribusi</div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-2">
            <div class="group block rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-medium text-zinc-500 group-hover:text-zinc-700 transition-colors">Total User MWC</div>
                        <div class="mt-2 flex items-baseline gap-1 text-3xl font-bold tracking-tight text-zinc-900">
                            {{ $totalMwcUsers ?? 0 }}
                            <span class="text-sm font-medium text-zinc-400">User</span>
                        </div>
                        <div class="mt-1 text-xs text-zinc-400">Terdaftar sebagai pengguna MWC</div>
                    </div>
                    <div class="rounded-2xl bg-emerald-50 p-4 text-emerald-600 transition-colors duration-300 group-hover:bg-emerald-100">
                        <i class="fa-solid fa-users text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="group block rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-medium text-zinc-500 group-hover:text-zinc-700 transition-colors">Total User Ranting</div>
                        <div class="mt-2 flex items-baseline gap-1 text-3xl font-bold tracking-tight text-zinc-900">
                            {{ $totalRantingUsers ?? 0 }}
                            <span class="text-sm font-medium text-zinc-400">User</span>
                        </div>
                        <div class="mt-1 text-xs text-zinc-400">Terdaftar sebagai pengguna Ranting</div>
                    </div>
                    <div class="rounded-2xl bg-amber-50 p-4 text-amber-600 transition-colors duration-300 group-hover:bg-amber-100">
                        <i class="fa-solid fa-user-group text-2xl"></i>
                    </div>
                </div>
            </div>
        </section>

        <script type="application/json" id="pc-chart-data">
            {!! $chartDataJson !!}
        </script>

        <section class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm lg:col-span-2">
                <h3 class="mb-4 text-sm font-semibold text-zinc-900">Trend Koin NU dan Infaq PC</h3>
                <div id="trendChartMwcRanting" class="min-h-[300px] w-full"></div>
            </div>

            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm lg:col-span-1">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-zinc-900">Distribusi Pengeluaran PC</h3>
                    <select id="filterDistribusi" class="rounded-xl border border-zinc-200 bg-white px-2 py-1.5 text-xs outline-none focus:border-green-300">
                        <option value="koin_nu">Koin NU</option>
                        <option value="infaq">Infaq PC</option>
                    </select>
                </div>
                <div id="donutChartDistribution" class="flex h-full min-h-[300px] w-full items-center justify-center"></div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-4">
            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-sm font-semibold text-zinc-900">Data Semua Transaksi</h3>
                    <div class="flex flex-wrap items-center gap-2">
                        <select id="filterJenis" class="rounded-2xl border border-zinc-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-green-300">
                            <option value="">Semua Jenis</option>
                            <option value="pemasukan-koin-nu">Pemasukan Koin NU PC</option>
                            <option value="pengeluaran-koin-nu">Pengeluaran Koin NU PC</option>
                            <option value="pemasukan-infaq">Pemasukan Infaq PC</option>
                            <option value="pengeluaran-infaq">Pengeluaran Infaq PC</option>
                        </select>
                        <input type="text" id="filterTanggal" placeholder="Pilih Rentang Tanggal" class="rounded-2xl border border-zinc-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-green-300">
                        <input type="text" id="search" placeholder="Cari transaksi..." class="w-full rounded-2xl border border-zinc-200 bg-white px-4 py-2.5 text-sm outline-none placeholder:text-zinc-400 focus:border-green-300 sm:w-48">
                    </div>
                </div>

                <div class="scrollbar-thin max-h-[320px] overflow-y-auto overflow-x-auto">
                    <table id="dataTable" class="w-full border-collapse">
                        <thead class="sticky top-0 z-10 bg-white shadow-[0_1px_0_0_rgba(244,244,245,1)]">
                            <tr class="text-left text-xs uppercase tracking-wide text-zinc-500">
                                <th class="px-3 py-3 font-semibold text-center">Kode Transaksi</th>
                                <th class="px-3 py-3 font-semibold text-center">Tanggal</th>
                                <th class="px-3 py-3 font-semibold text-center">Nama User</th>
                                <th class="px-3 py-3 font-semibold text-center">Role</th>
                                <th class="px-3 py-3 font-semibold text-center">Jenis</th>
                                <th class="px-3 py-3 font-semibold text-center">Tipe</th>
                                <th class="px-3 py-3 font-semibold text-center">Nominal</th>
                                <th class="px-3 py-3 font-semibold text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestTransactions as $trx)
                                <tr class="border-b border-zinc-100 text-sm text-zinc-700 trx-row"
                                    data-jenis="{{ $trx['jenis_filter'] }}"
                                    data-role="{{ strtolower($trx['role']) }}"
                                    data-tanggal="{{ \Carbon\Carbon::parse($trx['tanggal'])->format('Y-m-d') }}">
                                    <td class="px-3 py-3 text-center">{{ $trx['kode'] }}</td>
                                    <td class="px-3 py-3 text-center">{{ \Carbon\Carbon::parse($trx['tanggal'])->format('d M Y') }}</td>
                                    <td class="px-3 py-3 text-center font-medium">{{ $trx['user'] }}</td>
                                    <td class="px-3 py-3 text-center"><span class="capitalize">{{ $trx['role'] }}</span></td>
                                    <td class="px-3 py-3 text-center">{{ $trx['jenis_label'] }}</td>
                                    <td class="px-3 py-3 text-center">{{ $trx['tipe'] }}</td>
                                    <td class="px-3 py-3 text-center font-bold">Rp {{ number_format($trx['nominal'], 0, ',', '.') }}</td>
                                    <td class="px-3 py-3 text-center">
                                        @php
                                            $statusClass = [
                                                'validated' => 'bg-green-100 text-green-700',
                                                'rejected' => 'bg-red-100 text-red-700',
                                            ][$trx['status']] ?? 'bg-orange-100 text-orange-700';

                                            $statusLabel = [
                                                'validated' => 'Tervalidasi',
                                                'rejected' => 'Ditolak',
                                            ][$trx['status']] ?? 'Proses';
                                        @endphp
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold text-center {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-6 text-center text-sm text-zinc-500">Belum ada data transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection