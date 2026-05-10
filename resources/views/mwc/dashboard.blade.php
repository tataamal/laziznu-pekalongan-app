@extends('layouts.app')

@section('page_title', 'Dashboard MWC')
@section('page_subtitle', 'Kelola Persetujuan Laporan KOIN NU dan Transaksi Infaq.')

@section('content')
    @push('vite-scripts')
        @vite('resources/js/mwc-dashboard.js')
    @endpush

    <div class="w-full space-y-4">
        {{-- Stats Cards - Baris 1 & 2 --}}
        <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            {{-- Card 1: Saldo Koin NU MWC --}}
            <div id="card-koin-nu" class="relative rounded-3xl bg-gradient-to-br from-green-700 to-emerald-500 p-5 text-white shadow-sm transition-all duration-300 hover:shadow-lg cursor-pointer">
                <div class="text-sm font-medium text-white/90 flex items-center justify-between">
                    <span>Saldo Koin NU MWC {{ $wilayahName }}</span>
                    <i class="fa-solid fa-circle-info text-white/80"></i>
                </div>
                <div class="mt-4 text-3xl font-bold">Rp {{ number_format($totalKoinNuWilayah, 0, ',', '.') }}</div>
                <div class="mt-3 text-xs text-white/80">Total Saldo yang diperoleh dari 35% Koin NU Ranting</div>
            </div>

            {{-- Tooltip Box --}}
            <div id="tooltip-koin-nu" class="fixed pointer-events-none opacity-0 invisible transition-all duration-200 z-50 text-xs w-64 bg-white/95 backdrop-blur-md text-zinc-900 rounded-2xl p-4 shadow-2xl border border-zinc-200/50">
                <div class="font-semibold mb-2 border-b border-zinc-100 pb-1 text-zinc-700">Detail per Ranting</div>
                <div class="space-y-1.5 max-h-[150px] overflow-y-auto scrollbar-thin scrollbar-thumb-zinc-200">
                    @forelse($koinNuByRanting as $item)
                        <div class="flex justify-between items-center">
                            <span class="truncate pr-2 text-zinc-600">{{ $item->ranting ? 'Ranting ' . $item->ranting->nama_ranting : 'Transaksi MWC' }}</span>
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

            {{-- Card 2: Pengeluaran Koin NU MWC --}}
            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-zinc-600">Pengeluaran KOIN NU MWC {{ $wilayahName }}</div>
                <div class="mt-4 text-3xl font-bold tracking-tight text-zinc-900">Rp
                    {{ number_format($totalPengeluaranKoinNuMwc ?? 0, 0, ',', '.') }}</div>
                <div class="mt-3 text-xs text-zinc-500">Total pengeluaran dari saldo KOIN NU MWC {{ $wilayahName }}</div>
            </div>

            {{-- Card 3: Hak Amil Koin --}}
            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-zinc-600">Hak Amil Koin NU MWC {{ $wilayahName }}</div>
                <div class="mt-4 text-3xl font-bold tracking-tight text-zinc-900">Rp
                    {{ number_format($hakAmilKoinNuMwc ?? 0, 0, ',', '.') }}</div>
                <div class="mt-3 text-xs text-zinc-500">Total hak amil dari Koin NU MWC {{ $wilayahName }}</div>
            </div>

            {{-- Card 4: Saldo yang dapat digunakan MWC --}}
            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-zinc-600">Saldo KOIN NU yang dapat digunakan MWC {{ $wilayahName }}</div>
                <div class="mt-4 text-3xl font-bold tracking-tight text-zinc-900">Rp
                    {{ number_format($dana_koin_nu_dapat_digunakan_mwc ?? 0, 0, ',', '.') }}</div>
                <div class="mt-3 text-xs text-zinc-500">Total saldo yang dapat digunakan MWC {{ $wilayahName }}</div>
            </div>

            {{-- Card 5: Saldo Infaq MWC --}}
            <div class="rounded-3xl bg-gradient-to-br from-yellow-700 to-amber-500 p-5 text-white shadow-sm">
                <div class="text-sm font-medium text-white/90">Saldo Infaq MWC {{ $wilayahName }}</div>
                <div class="mt-4 text-3xl font-bold">Rp {{ number_format($totalPemasukanInfaqMwc, 0, ',', '.') }}</div>
                <div class="mt-3 text-xs text-white/80">Total Saldo Infaq MWC {{ $wilayahName }}</div>
            </div>

            {{-- Card 6: Pengeluaran Infaq MWC --}}
            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-zinc-600">Pengeluaran Infaq MWC {{ $wilayahName }}</div>
                <div class="mt-4 text-3xl font-bold tracking-tight text-zinc-900">Rp
                    {{ number_format($totalPengeluaranInfaqMwc ?? 0, 0, ',', '.') }}</div>
                <div class="mt-3 text-xs text-zinc-500">Total pengeluaran dari saldo Infaq MWC {{ $wilayahName }}</div>
            </div>

            {{-- Card 7: Hak Amil Infaq MWC --}}
            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-zinc-600">Hak Amil Infaq MWC {{ $wilayahName }}</div>
                <div class="mt-4 text-3xl font-bold tracking-tight text-zinc-900">Rp
                    {{ number_format($hakAmilInfaqMwc ?? 0, 0, ',', '.') }}</div>
                <div class="mt-3 text-xs text-zinc-500">Total hak amil dari Infaq MWC {{ $wilayahName }}</div>
            </div>

            {{-- Card 8: Infaq yang dapat digunakan MWC --}}
            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-zinc-600">Infaq yang dapat digunakan MWC {{ $wilayahName }}</div>
                <div class="mt-4 text-3xl font-bold tracking-tight text-zinc-900">Rp
                    {{ number_format($infaq_dapat_digunakan_mwc ?? 0, 0, ',', '.') }}</div>
                <div class="mt-3 text-xs text-zinc-500">Total infaq yang dapat digunakan MWC {{ $wilayahName }}</div>
            </div>
        </section>
        <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-2">
            {{-- Card 5: Approval Koin --}}
            <a href="{{ route('mwc.approval-income-koin-nu') }}" class="group block rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300 cursor-pointer">
                <div class="flex justify-between items-center">
                    {{-- Text Section --}}
                    <div>
                        <div class="text-sm font-medium text-zinc-500 group-hover:text-zinc-700 transition-colors">Approval KOIN NU</div>
                        <div class="mt-2 text-3xl font-bold tracking-tight text-zinc-900 flex items-baseline gap-1">
                            {{ $pendingIncomesCount }}
                            <span class="text-sm font-medium text-zinc-400">Laporan</span>
                        </div>
                        <div class="mt-1 text-xs text-zinc-400 flex items-center gap-1">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                            </span>
                            Menunggu persetujuan
                        </div>
                    </div>
                    
                    {{-- Icon Section --}}
                    <div class="bg-orange-50 text-orange-500 p-4 rounded-2xl group-hover:bg-orange-100 group-hover:text-orange-600 transition-colors duration-300">
                        <i class="fa-solid fa-bell text-2xl"></i>
                    </div>
                </div>
            </a>

            {{-- Card 6: Approval Pentasarufan --}}
            <a href="{{ route('mwc.approval-distribution-koin-nu') }}" class="group block rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300 cursor-pointer">
                <div class="flex justify-between items-center">
                    {{-- Text Section --}}
                    <div>
                        <div class="text-sm font-medium text-zinc-500 group-hover:text-zinc-700 transition-colors">Approval Pentasarufan</div>
                        <div class="mt-2 text-3xl font-bold tracking-tight text-zinc-900 flex items-baseline gap-1">
                            {{ $pendingDistributionsCount }}
                            <span class="text-sm font-medium text-zinc-400">Laporan</span>
                        </div>
                        <div class="mt-1 text-xs text-zinc-400 flex items-center gap-1">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                            </span>
                            Menunggu persetujuan
                        </div>
                    </div>
                    
                    {{-- Icon Section --}}
                    <div class="bg-orange-50 text-orange-500 p-4 rounded-2xl group-hover:bg-orange-100 group-hover:text-orange-600 transition-colors duration-300">
                        <i class="fa-solid fa-bell text-2xl"></i>
                    </div>
                </div>
            </a>
        </section>

        {{-- Chart JSON Data --}}
        <script type="application/json" id="mwc-chart-data">
            {!! $chartDataJson !!}
        </script>

        {{-- Charts Section --}}
        <section class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm lg:col-span-2">
                <h3 class="mb-4 text-sm font-semibold text-zinc-900">Trend Transaksi Infaq MWC</h3>
                <div id="lineChartIncome" class="min-h-[300px] w-full"></div>
            </div>

            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm lg:col-span-1">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-sm font-semibold text-zinc-900">Distribusi Pentasarufan</h3>
                    <select id="filterDonut" class="rounded-xl border border-zinc-200 bg-white px-2 py-1 text-xs outline-none focus:border-green-300">
                        <option value="koin">Koin NU</option>
                        <option value="infaq">Infaq MWC</option>
                    </select>
                </div>
                <div id="donutChartDistribution" class="flex h-full min-h-[300px] w-full items-center justify-center"></div>
            </div>
        </section>

        {{-- Table Section --}}
        <section class="grid grid-cols-1 gap-4">
            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-sm font-semibold text-zinc-900">Data Semua Transaksi</h3>
                    <div class="flex flex-wrap items-center gap-2">
                        <select id="filterJenis"
                            class="rounded-2xl border border-zinc-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-green-300">
                            <option value="">Semua Jenis</option>
                            <option value="koin">Transaksi Koin NU</option>
                            <option value="infaq">Transaksi Infaq MWC</option>
                        </select>
                        <input type="text" id="filterTanggal" placeholder="Pilih Rentang Tanggal"
                            class="rounded-2xl border border-zinc-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-green-300">
                        <input type="text" id="search" placeholder="Cari transaksi..."
                            class="w-full rounded-2xl border border-zinc-200 bg-white px-4 py-2.5 text-sm outline-none placeholder:text-zinc-400 focus:border-green-300 sm:w-48">
                    </div>
                </div>

                <div class="scrollbar-thin max-h-[320px] overflow-y-auto overflow-x-auto">
                    <table id="dataTable" class="w-full border-collapse">
                        <thead class="sticky top-0 z-10 bg-white shadow-[0_1px_0_0_rgba(244,244,245,1)]">
                            <tr class="text-left text-xs uppercase tracking-wide text-zinc-500">
                                <th class="px-3 py-3 font-semibold text-center">Kode Transaksi</th>
                                <th class="px-3 py-3 font-semibold text-center">Tanggal</th>
                                <th class="px-3 py-3 font-semibold text-center">Nama User</th>
                                <th class="px-3 py-3 font-semibold text-center">Sumber Dana</th>
                                <th class="px-3 py-3 font-semibold text-center">Jenis Transaksi</th>
                                <th class="px-3 py-3 font-semibold text-center">Tipe Transaksi</th>
                                <th class="px-3 py-3 font-semibold text-center">Jumlah Penerima Manfaat</th>
                                <th class="px-3 py-3 font-semibold text-center">Nominal Total</th>
                                <th class="px-3 py-3 font-semibold text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestTransactions as $trx)
                                <tr class="border-b border-zinc-100 text-sm text-zinc-700 trx-row"
                                    data-jenis="{{ $trx['jenis_filter'] }}" data-role="{{ strtolower($trx['role']) }}"
                                    data-tanggal="{{ \Carbon\Carbon::parse($trx['tanggal'])->format('Y-m-d') }}">
                                    <td class="px-3 py-3 text-center">{{ $trx['kode'] }}</td>
                                    <td class="px-3 py-3 text-center">{{ \Carbon\Carbon::parse($trx['tanggal'])->format('d M Y') }}
                                    </td>
                                    <td class="px-3 py-3 font-medium text-center">{{ $trx['user'] }}</td>
                                    <td class="px-3 py-3 text-center"><span class="capitalize">{{ $trx['role'] }}</span></td>
                                    <td class="px-3 py-3 text-center">{{ $trx['jenis_label'] }}</td>
                                    <td class="px-3 py-3 text-center">{{ $trx['tipe_transaksi'] }}</td>
                                    <td class="px-3 py-3 text-center">
                                        @if (isset($trx['penerima']) && $trx['penerima'] > 0)
                                            <div class="font-bold text-zinc-900">
                                                {{ number_format($trx['penerima'], 0, ',', '.') }}</div>
                                            <div class="text-[10px] text-zinc-500 uppercase font-medium">Jiwa</div>
                                        @else
                                            <span class="text-zinc-400">Tipe Transaksi Pemasukan</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-center font-bold">Rp
                                        {{ number_format($trx['nominal'], 0, ',', '.') }}</td>
                                    <td class="px-3 py-3 text-center">
                                        @php
                                            $statusClass =
                                                [
                                                    'validated' => 'bg-green-100 text-green-700',
                                                    'rejected' => 'bg-red-100 text-red-700',
                                                ][$trx['status']] ?? 'bg-orange-100 text-orange-700';

                                            $statusLabel =
                                                [
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
                                    <td colspan="8" class="px-3 py-6 text-center text-sm text-zinc-500">Belum ada data
                                        transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection
