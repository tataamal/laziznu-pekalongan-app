@extends('layouts.app')

@section('page_title', 'Dashboard PC')
@section('page_subtitle', 'Monitoring Cashfolow KOIN NU dan Infaq Kabupaten Pekalongan.')

@section('content')
    @push('vite-scripts')
        @vite('resources/js/pc-dashboard.js')
    @endpush

    <section class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="rounded-3xl bg-gradient-to-br from-green-700 to-emerald-500 p-5 text-white shadow-sm">
            <div class="text-sm font-medium text-white/90">Total Saldo Infaq PC</div>
            <div class="mt-4 text-3xl font-bold">Rp {{ number_format($totalSaldoPc, 0, ',', '.') }}</div>
            <div class="mt-3 text-xs text-white/80">Saldo akumulasi dari Infaq PC</div>
        </div>

        <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
            <div class="text-sm font-medium text-zinc-600">Hak Amil KOIN NU</div>
            <div class="mt-4 text-3xl font-bold tracking-tight text-green-700">Rp {{ number_format($totalHakAmilPcKoinNu, 0, ',', '.') }}</div>
            <div class="mt-3 text-xs text-zinc-500">Total saldo hak amil PC</div>
        </div>

        <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
            <div class="text-sm font-medium text-zinc-600">Total Pengguna MWC</div>
            <div class="mt-4 text-3xl font-bold tracking-tight text-zinc-900">{{ $totalMwcUsers }}</div>
            <div class="mt-3 text-xs text-zinc-500">Jumlah user dengan role MWC</div>
        </div>

        <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
            <div class="text-sm font-medium text-zinc-600">Total Pengguna Ranting</div>
            <div class="mt-4 text-3xl font-bold tracking-tight text-zinc-900">{{ $totalRantingUsers }}</div>
            <div class="mt-3 text-xs text-zinc-500">Jumlah user dengan role Ranting</div>
        </div>
    </section>

    {{-- Chart JSON Data --}}
    <script type="application/json" id="pc-chart-data">
        {!! $chartDataJson !!}
    </script>

    <section class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
            <h3 class="mb-4 text-sm font-semibold text-zinc-900">Trend Pemasukan MWC & Ranting 6 Bulan Terakhir</h3>
            <div id="trendChartMwcRanting" class="min-h-[350px] w-full"></div>
        </div>

        <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
            <h3 class="mb-4 text-sm font-semibold text-zinc-900">Trend Saldo PC 6 Bulan Terakhir</h3>
            <div id="trendChartPc" class="min-h-[350px] w-full"></div>
        </div>
    </section>

    <section class="mt-4">
        <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
            <h3 class="mb-4 text-sm font-semibold text-zinc-900">Distribusi Pengeluaran Infaq</h3>
            <div id="donutChartDistribution" class="min-h-[350px] w-full flex items-center justify-center"></div>
        </div>
    </section>

    <section class="mt-4 grid grid-cols-1 gap-4">
        <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-sm font-semibold text-zinc-900">Data Semua Transaksi</h3>
                <div class="flex flex-wrap items-center gap-2">
                    <select id="filterJenis" class="rounded-2xl border border-zinc-200 bg-white px-3 py-2.5 text-sm outline-none ring-0 focus:border-green-300">
                        <option value="">Semua Jenis</option>
                        <option value="pemasukan">Pemasukan</option>
                        <option value="pengeluaran">Pengeluaran</option>
                    </select>

                    <select id="filterRole" class="rounded-2xl border border-zinc-200 bg-white px-3 py-2.5 text-sm outline-none ring-0 focus:border-green-300">
                        <option value="">Semua Role</option>
                        <option value="developer">Developer</option>
                        <option value="pc">PC</option>
                        <option value="mwc">MWC</option>
                        <option value="ranting">Ranting</option>
                    </select>

                    <input type="text" id="filterTanggal" placeholder="Pilih Rentang Tanggal" class="rounded-2xl border border-zinc-200 bg-white px-3 py-2.5 text-sm outline-none ring-0 focus:border-green-300">

                    <input
                        type="text"
                        id="search"
                        placeholder="Cari transaksi..."
                        class="w-full rounded-2xl border border-zinc-200 bg-white px-4 py-2.5 text-sm outline-none ring-0 placeholder:text-zinc-400 focus:border-green-300 sm:w-48"
                    >
                    <button
                        onclick="exportTable()"
                        class="rounded-2xl bg-green-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800"
                    >
                        Export
                    </button>
                </div>
            </div>

            <div class="scrollbar-thin scrollbar-thumb-zinc-200 hover:scrollbar-thumb-zinc-300 max-h-[320px] overflow-y-auto overflow-x-auto">
                <table id="dataTable" class="w-full border-collapse">
                    <thead class="sticky top-0 z-10 bg-white shadow-[0_1px_0_0_rgba(244,244,245,1)]">
                        <tr class="text-left text-xs uppercase tracking-wide text-zinc-500">
                            <th class="px-3 py-3 font-semibold">Kode</th>
                            <th class="px-3 py-3 font-semibold">Tanggal</th>
                            <th class="px-3 py-3 font-semibold">User</th>
                            <th class="px-3 py-3 font-semibold">Role</th>
                            <th class="px-3 py-3 font-semibold">Jenis</th>
                            <th class="px-3 py-3 font-semibold text-right">Nominal</th>
                            <th class="px-3 py-3 font-semibold text-center">Tipe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestTransactions as $trx)
                        <tr class="border-b border-zinc-100 text-sm text-zinc-700 trx-row" 
                            data-jenis="{{ $trx['jenis_filter'] }}" 
                            data-role="{{ strtolower($trx['role']) }}" 
                            data-tanggal="{{ \Carbon\Carbon::parse($trx['tanggal'])->format('Y-m-d') }}">
                            <td class="px-3 py-3">{{ $trx['kode'] }}</td>
                            <td class="px-3 py-3">{{ \Carbon\Carbon::parse($trx['tanggal'])->format('d M Y') }}</td>
                            <td class="px-3 py-3 font-medium">{{ $trx['user'] }}</td>
                            <td class="px-3 py-3"><span class="capitalize">{{ $trx['role'] }}</span></td>
                            <td class="px-3 py-3">{{ $trx['jenis_label'] }}</td>
                            <td class="px-3 py-3 text-right">Rp {{ number_format($trx['nominal'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">
                                @if($trx['tipe'] === 'Pemasukan')
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Pemasukan</span>
                                @else
                                    <span class="rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700">Pengeluaran</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-3 py-6 text-center text-sm text-zinc-500">
                                Belum ada data transaksi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection