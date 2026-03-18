@extends('layouts.app')

@section('page_title', 'Data Transaksi MWC')
@section('page_subtitle', 'Seluruh riwayat transaksi infaq dari seluruh MWC.')

@section('content')
    <div class="w-full space-y-8">
        {{-- Header & Stats --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-2xl bg-green-700 text-white flex items-center justify-center shadow-lg shadow-green-200">
                    <i class="fas fa-building-columns text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Transmit Infaq MWC</h2>
                    <p class="text-sm text-slate-500">Total {{ $items->count() }} transaksi tercatat secara global</p>
                </div>
            </div>
        </div>

        {{-- Filter & Search Section --}}
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
            <form action="{{ route('pc.data-transaksi-mwc') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
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

                {{-- Global Search --}}
                <div class="space-y-2">
                    <label for="searchInput" class="text-xs font-semibold uppercase tracking-wider text-slate-500 ml-1">Cari Transaksi</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input 
                            type="text" 
                            id="searchInput" 
                            placeholder="Cari kode atau jenis..." 
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 py-3 pl-11 pr-4 text-sm transition focus:border-green-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-green-500/10"
                        >
                    </div>
                </div>

                {{-- Export/Action (Placeholders) --}}
                <div class="flex items-end">
                    <button type="button" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-slate-100 px-4 py-3 text-sm font-bold text-slate-600 transition-all hover:bg-slate-200">
                        <i class="fas fa-file-excel"></i>
                        <span>Export Data</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Table Section --}}
        <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden text-sm">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse" id="dataMwcTable">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Info Transaksi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Wilayah</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Jenis Transaksi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Pemasukan Total</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Dapat Disalurkan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($items as $item)
                            <tr class="hover:bg-slate-50/50 transition-colors mwc-row" data-search="{{ strtolower($item->transaction_code . ' ' . $item->infaq_type) }}">
                                <td class="px-6 py-5">
                                    <div class="font-bold text-slate-900 leading-tight">{{ $item->transaction_code }}</div>
                                    <div class="text-[11px] text-slate-500 mt-1 uppercase font-medium">
                                        {{ \Carbon\Carbon::parse($item->transaction_date)->format('d M Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-semibold text-slate-800">{{ $item->user->name }}</div>
                                    <div class="text-[11px] text-green-600 font-bold uppercase">{{ $item->user->wilayah ? $item->user->wilayah->nama_wilayah : '-' }}</div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex items-center rounded-lg {{ $item->transaction_type === 'Pemasukan' ? 'bg-green-50 text-green-700 border-green-100' : 'bg-red-50 text-red-700 border-red-100' }} border px-2 py-0.5 text-[10px] font-bold mb-1 uppercase">
                                        {{ $item->transaction_type }}
                                    </span>
                                    <div class="font-medium text-slate-700">{{ $item->infaq_type }}</div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-slate-900">Rp {{ number_format($item->gross_amount, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-green-700">Rp {{ number_format($item->allowed_budget, 0, ',', '.') }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center text-slate-400">
                                    Belum ada data transaksi MWC.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('vite-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const rows = document.querySelectorAll('.mwc-row');

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
