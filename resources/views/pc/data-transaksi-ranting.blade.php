@extends('layouts.app')

@section('page_title', 'Data Transaksi Ranting')
@section('page_subtitle', 'Seluruh laporan pemasukan KOIN NU dari seluruh Ranting.')

@section('content')
    <div class="w-full space-y-8">
        {{-- Header & Stats --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-2xl bg-green-700 text-white flex items-center justify-center shadow-lg shadow-green-200">
                    <i class="fas fa-users-gear text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Laporan KOIN NU Ranting</h2>
                    <p class="text-sm text-slate-500">Total {{ $pemasukans->count() + $pengeluarans->count() }} laporan tercatat secara global</p>
                </div>
            </div>
        </div>

        {{-- Filter & Search Section --}}
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
            <form action="{{ route('pc.data-transaksi-ranting') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Wilayah Filter --}}
                <div class="space-y-2">
                    <label for="wilayah_id" class="text-xs font-semibold uppercase tracking-wider text-slate-500 ml-1">Filter Wilayah</label>
                    <div class="relative">
                        <i class="fas fa-map-marker-alt absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <select 
                            name="wilayah_id" 
                            id="wilayah_id" 
                            onchange="this.form.submit()"
                            class="w-full appearance-none rounded-2xl border border-slate-200 bg-slate-50/50 py-3 pl-11 pr-10 text-sm transition focus:border-green-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-green-500/10"
                        >
                            <option value="">Semua Wilayah</option>
                            @foreach($wilayahs as $wilayah)
                                <option value="{{ $wilayah->id }}" {{ $wilayahId == $wilayah->id ? 'selected' : '' }}>
                                    {{ $wilayah->nama_wilayah }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                    </div>
                </div>

                {{-- Transaction Type Filter --}}
                <div class="space-y-2">
                    <label for="transaction_type" class="text-xs font-semibold uppercase tracking-wider text-slate-500 ml-1">Jenis Transaksi</label>
                    <div class="relative">
                        <i class="fas fa-exchange-alt absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <select 
                            name="transaction_type" 
                            id="transaction_type" 
                            onchange="this.form.submit()"
                            class="w-full appearance-none rounded-2xl border border-slate-200 bg-slate-50/50 py-3 pl-11 pr-10 text-sm transition focus:border-green-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-green-500/10"
                        >
                            <option value="">Semua Transaksi</option>
                            <option value="Pemasukan" {{ request('transaction_type') == 'Pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                            <option value="Pengeluaran" {{ request('transaction_type') == 'Pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                    </div>
                </div>

                {{-- Global Search --}}
                <div class="space-y-2">
                    <label for="searchInput" class="text-xs font-semibold uppercase tracking-wider text-slate-500 ml-1">Cari Laporan</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input 
                            type="text" 
                            id="searchInput" 
                            placeholder="Cari kode atau nama ranting..." 
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 py-3 pl-11 pr-4 text-sm transition focus:border-green-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-green-500/10"
                        >
                    </div>
                </div>
            </form>
        </div>

        {{-- Tabs Section (Optional, using stacked tables for now) --}}
        <div class="space-y-8">
            @if(request('transaction_type') != 'Pengeluaran')
            {{-- Table Section: Pemasukan --}}
            <div>
                <h3 class="text-lg font-bold text-slate-800 mb-4 ml-1">Riwayat Pemasukan</h3>
                <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden text-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse" id="dataRantingTablePemasukan">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-100">
                                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Kode Laporan</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Ranting / Wilayah</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Pemasukan Kotor</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Hak Amil</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Pemasukan Bersih</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Dana Dapat digunakan Ranting</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($pemasukans as $item)
                                    <tr class="hover:bg-slate-50/50 transition-colors ranting-row" data-search="{{ strtolower($item->transaction_code . ' ' . $item->user->name) }}">
                                        <td class="px-6 py-5">
                                            <div class="font-bold text-slate-900 leading-tight">{{ $item->transaction_code }}</div>
                                            <div class="text-[11px] text-slate-500 mt-1 uppercase font-medium">
                                                {{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="font-semibold text-slate-800">{{ $item->user->name }}</div>
                                            <div class="text-[11px] text-green-600 font-bold uppercase">{{ $item->user->wilayah ? $item->user->wilayah->nama_wilayah : '-' }}</div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="font-bold text-slate-900">Rp {{ number_format($item->gross_profit, 0, ',', '.') }}</div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="font-bold text-slate-700">Rp {{ number_format($item->hak_amil, 0, ',', '.') }}</div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="font-bold text-slate-700">Rp {{ number_format($item->net_income, 0, ',', '.') }}</div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="font-bold text-green-700">Rp {{ number_format($item->allowed_budget, 0, ',', '.') }}</div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span class="inline-flex items-center rounded-lg {{ $item->status === 'Validated' ? 'bg-green-50 text-green-700 border-green-100' : 'bg-amber-50 text-amber-700 border-amber-100' }} border px-2 py-0.5 text-[10px] font-bold uppercase">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-20 text-center text-slate-400">
                                            Belum ada data pemasukan Ranting.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            @if(request('transaction_type') != 'Pemasukan')
            {{-- Table Section: Pengeluaran --}}
            <div>
                <h3 class="text-lg font-bold text-slate-800 mb-4 ml-1">Riwayat Pengeluaran</h3>
                <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden text-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse" id="dataRantingTablePengeluaran">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-100">
                                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Kode Transaksi</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Ranting / Wilayah</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Event / Program</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Penerima Manfaat</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Nominal Pengeluaran</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($pengeluarans as $item)
                                    <tr class="hover:bg-slate-50/50 transition-colors ranting-row" data-search="{{ strtolower($item->transaction_code . ' ' . $item->user->name . ' ' . $item->event_name) }}">
                                        <td class="px-6 py-5">
                                            <div class="font-bold text-slate-900 leading-tight">{{ $item->transaction_code }}</div>
                                            <div class="text-[11px] text-slate-500 mt-1 uppercase font-medium">
                                                {{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="font-semibold text-slate-800">{{ $item->user->name }}</div>
                                            <div class="text-[11px] text-green-600 font-bold uppercase">{{ $item->user->wilayah ? $item->user->wilayah->nama_wilayah : '-' }}</div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="font-bold text-slate-900">{{ $item->event_name }}</div>
                                            <div class="text-[11px] text-slate-500 mt-1 uppercase font-medium">
                                                {{ $item->pilar_type }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="font-medium text-slate-700">{{ $item->penerima_manfaat ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="font-bold text-red-700">Rp {{ number_format($item->cost_amount, 0, ',', '.') }}</div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span class="inline-flex items-center rounded-lg {{ $item->status === 'Approved' ? 'bg-green-50 text-green-700 border-green-100' : 'bg-amber-50 text-amber-700 border-amber-100' }} border px-2 py-0.5 text-[10px] font-bold uppercase">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-20 text-center text-slate-400">
                                            Belum ada data pengeluaran Ranting.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @push('vite-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const rows = document.querySelectorAll('.ranting-row');

            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                rows.forEach(row => {
                    const searchData = row.getAttribute('data-search');
                    if (searchData.includes(query)) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });
            });
        });
    </script>
    @endpush
@endsection
