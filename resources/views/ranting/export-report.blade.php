@extends('layouts.app')

@section('page_title', 'Export Data Pentasarufan')
@section('page_subtitle', 'Preview and export your pentasarufan data to Excel.')

@section('content')
    <div class="w-full space-y-6">
        <!-- Filter Card -->
        <div class="bg-white border border-zinc-200 rounded-3xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-zinc-100 bg-zinc-50/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-green-100 text-green-700 rounded-xl">
                        <i class="fas fa-filter text-sm"></i>
                    </div>
                    <h2 class="text-lg font-bold text-zinc-800">Filter Data</h2>
                </div>
            </div>

            <div class="p-6">
                <form action="{{ route('ranting.export-report.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                    <div>
                        <label for="start_date" class="block text-xs font-bold text-zinc-500 uppercase tracking-wider mb-2">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" 
                               class="w-full rounded-2xl border-zinc-200 focus:border-green-500 focus:ring-green-500 text-sm py-2.5">
                    </div>
                    <div>
                        <label for="end_date" class="block text-xs font-bold text-zinc-500 uppercase tracking-wider mb-2">Tanggal Selesai</label>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                               class="w-full rounded-2xl border-zinc-200 focus:border-green-500 focus:ring-green-500 text-sm py-2.5">
                    </div>
                    <div class="md:col-span-2 flex gap-3 mt-2">
                        <button type="submit" class="flex-1 bg-green-700 hover:bg-green-800 text-white font-bold py-3 px-6 rounded-2xl transition shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                            <i class="fas fa-search"></i>
                            <span>Tampilkan Preview</span>
                        </button>
                        @if($distributions->isNotEmpty())
                            <button type="submit" form="export-form" class="flex-1 bg-zinc-800 hover:bg-zinc-900 text-white font-bold py-3 px-6 rounded-2xl transition shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                <i class="fas fa-file-excel"></i>
                                <span>Export ke Excel</span>
                            </button>
                        @endif
                    </div>
                </form>

                <form id="export-form" action="{{ route('ranting.export-report.export') }}" method="POST" style="display: none;">
                    @csrf
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                </form>
            </div>
        </div>

        <!-- Preview Table Card -->
        <div class="bg-white border border-zinc-200 rounded-3xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-zinc-100 bg-zinc-50/50 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 text-blue-700 rounded-xl">
                        <i class="fas fa-table text-sm"></i>
                    </div>
                    <h2 class="text-lg font-bold text-zinc-800">Preview Data</h2>
                </div>
                @if($distributions->isNotEmpty())
                    <span class="px-4 py-1.5 bg-green-100 text-green-700 text-xs font-bold rounded-full">
                        {{ $distributions->count() }} Data Ditemukan
                    </span>
                @endif
            </div>

            <div class="p-6">
                <div class="overflow-x-auto rounded-2xl border border-zinc-100">
                    <table class="min-w-full divide-y divide-zinc-200">
                        <thead>
                            <tr class="bg-zinc-50/50">
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-zinc-500">No</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-zinc-500">Tanggal</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-zinc-500">Kode</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-zinc-500">Penerima</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-zinc-500">Keterangan</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-zinc-500">Jenis</th>
                                <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-zinc-500">Nominal</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-zinc-500">Wilayah</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-zinc-100">
                            @forelse($distributions as $index => $item)
                                <tr class="hover:bg-zinc-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600">{{ $item['date'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-zinc-800">{{ $item['transaction_code'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600">{{ $item['penerima_manfaat'] }}</td>
                                    <td class="px-6 py-4 text-sm text-zinc-600 max-w-xs truncate" title="{{ $item['event_name'] }}">
                                        {{ $item['event_name'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full {{ $item['type'] === 'Koin NU' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $item['type'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-zinc-900 text-right">
                                        Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600">{{ $item['wilayah'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-zinc-500">
                                        <div class="flex flex-col items-center gap-2">
                                            <i class="fas fa-folder-open text-4xl text-zinc-200"></i>
                                            <p class="text-sm font-medium">Belum ada data dipilih atau tidak ada data yang sesuai filter.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($distributions->isNotEmpty())
                            <tfoot class="bg-zinc-50/50 font-bold border-t-2 border-zinc-100">
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-right text-xs uppercase tracking-wider text-zinc-500">Total Pentasarufan</td>
                                    <td class="px-6 py-4 text-right text-base text-green-700">
                                        Rp {{ number_format($distributions->sum('amount'), 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
