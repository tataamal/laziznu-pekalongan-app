@extends('layouts.app')

@section('page_title', 'Approval Pentasarufan Koin NU')
@section('page_subtitle', 'Tinjau dan setujui laporan pentasarufan (pengeluaran) dari Ranting.')

@section('content')
    <div class="w-full space-y-8">
        {{-- Filter & Search Section --}}
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <h3 class="text-lg font-bold text-slate-800">Filter Data</h3>
                {{-- Stats Summary --}}
                <div class="px-6 py-3 bg-amber-50 rounded-2xl border border-amber-100 flex items-center gap-4">
                    <div class="h-10 w-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase text-amber-500 tracking-tight">Menunggu Persetujuan</div>
                        <div class="text-xl font-bold text-amber-900 leading-none">{{ $items->count() }} <span class="text-xs font-medium text-amber-700/60 uppercase ml-1">Laporan</span></div>
                    </div>
                </div>
            </div>

            <form action="{{ route('mwc.approval-distribution-koin-nu') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Kode Transaksi --}}
                    <div class="space-y-2">
                        <label for="transaction_code" class="text-xs font-semibold uppercase tracking-wider text-slate-500 ml-1">Kode Transaksi</label>
                        <div class="relative">
                            <i class="fas fa-barcode absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input 
                                type="text" 
                                name="transaction_code" 
                                id="transaction_code" 
                                value="{{ request('transaction_code') }}"
                                placeholder="Kode Transaksi" 
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 py-3 pl-11 pr-4 text-sm transition focus:border-green-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-green-500/10"
                            >
                        </div>
                    </div>

                    {{-- Nama Ranting --}}
                    <div class="space-y-2">
                        <label for="ranting_name" class="text-xs font-semibold uppercase tracking-wider text-slate-500 ml-1">Nama Ranting</label>
                        <div class="relative">
                            <i class="fas fa-mosque absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input 
                                type="text" 
                                name="ranting_name" 
                                id="ranting_name" 
                                value="{{ request('ranting_name') }}"
                                placeholder="Nama Ranting" 
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 py-3 pl-11 pr-4 text-sm transition focus:border-green-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-green-500/10"
                            >
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="space-y-2">
                        <label for="status" class="text-xs font-semibold uppercase tracking-wider text-slate-500 ml-1">Status</label>
                        <div class="relative">
                            <i class="fas fa-info-circle absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <select 
                                name="status" 
                                id="status" 
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 py-3 pl-11 pr-4 text-sm transition focus:border-green-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-green-500/10 appearance-none"
                            >
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                                <option value="on_process" {{ request('status') == 'on_process' ? 'selected' : '' }}>Menunggu</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                    </div>

                    {{-- Date Filter --}}
                    <div class="space-y-2">
                        <label for="date_range" class="text-xs font-semibold uppercase tracking-wider text-slate-500 ml-1">Filter Tanggal</label>
                        <div class="relative">
                            <i class="fas fa-calendar-alt absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input 
                                type="text" 
                                name="date_range" 
                                id="date_range" 
                                value="{{ request('date_range') }}"
                                placeholder="Pilih rentang tanggal..." 
                                autocomplete="off"
                                class="flatpickr-date w-full rounded-2xl border border-slate-200 bg-slate-50/50 py-3 pl-11 pr-4 text-sm transition focus:border-green-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-green-500/10"
                            >
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-4 gap-2">
                    <a href="{{ route('mwc.approval-distribution-koin-nu') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-200 transition">Reset</a>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-xl hover:bg-green-700 transition">Cari</button>
                </div>
            </form>
        </div>

        {{-- Table Section --}}
        <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse" id="approvalTable">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Info Transaksi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Ranting Asal</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Item Pentasarufan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Pilar & Nominal</th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500">Penerima</th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500 w-[200px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($items as $item)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-5">
                                    <div class="font-bold text-slate-900 leading-tight">{{ $item->transaction_code }}</div>
                                    <div class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                                        <i class="far fa-calendar-alt text-[10px]"></i>
                                        {{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div>
                                            <div class="font-semibold text-slate-800 text-sm">{{ $item->user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-slate-800 text-sm mb-1">{{ $item->event_name }}</div>
                                    @if($item->documentation_file)
                                        <button 
                                            onclick="viewPhoto('{{ asset('storage/' . $item->documentation_file) }}', '{{ $item->event_name }}')"
                                            class="group relative inline-block overflow-hidden rounded-xl border border-slate-200 p-1 hover:border-green-500 transition-all shadow-sm"
                                        >
                                            <img src="{{ asset('storage/' . $item->documentation_file) }}" alt="Doc" class="h-10 w-16 object-cover rounded-lg group-hover:scale-110 transition-transform">
                                            <div class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <i class="fas fa-eye text-white text-[10px]"></i>
                                            </div>
                                        </button>
                                    @else
                                        <span class="text-[10px] text-slate-400 font-medium italic italic">No documentation</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5">
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter mb-1">{{ $item->pilar_type }}</div>
                                    <div class="font-bold text-red-600">Rp {{ number_format($item->cost_amount, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                        {{ $item->penerima_manfaat ?? 0 }} Org
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Approve Button --}}
                                        <form action="{{ route('mwc.approval-distribution-koin-nu.approve', $item->id) }}" method="POST" id="approve-form-{{ $item->id }}">
                                            @csrf
                                            <button 
                                                type="button" 
                                                onclick="confirmAction('approve-form-{{ $item->id }}', 'Setujui Pentasarufan?', 'Laporan ini akan divalidasi dan tercatat sebagai pengeluaran resmi.', 'success', 'Ya, Setujui')"
                                                class="h-10 w-10 flex items-center justify-center rounded-xl bg-green-600 text-white hover:bg-green-700 shadow-sm shadow-green-200 transition-all hover:scale-110"
                                                title="Setujui Laporan"
                                            >
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>

                                        {{-- Reject Button --}}
                                        <form action="{{ route('mwc.approval-distribution-koin-nu.reject', $item->id) }}" method="POST" id="reject-form-{{ $item->id }}">
                                            @csrf
                                            <button 
                                                type="button" 
                                                onclick="confirmAction('reject-form-{{ $item->id }}', 'Tolak Laporan?', 'Laporan ini akan ditandai sebagai ditolak.', 'error', 'Ya, Tolak')"
                                                class="h-10 w-10 flex items-center justify-center rounded-xl bg-red-100 text-red-600 hover:bg-red-600 hover:text-white border border-red-200 transition-all hover:scale-110"
                                                title="Tolak Laporan"
                                            >
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center opacity-40">
                                        <div class="h-20 w-20 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                            <i class="fas fa-box-open text-3xl text-slate-400"></i>
                                        </div>
                                        <p class="text-slate-500 font-medium tracking-tight">Tidak ada pentasarufan yang menunggu persetujuan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- History Section --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-history text-green-600"></i>
                    Riwayat Approval
                </h2>
                <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded-full uppercase tracking-wider">50 Terakhir</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-100">
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Info Transaksi</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Ranting</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Kegiatan</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Nominal</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500">Penerima</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($historyItems as $item)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900 text-xs">{{ $item->transaction_code }}</div>
                                        <div class="text-[10px] text-slate-500 mt-0.5">{{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-slate-700 text-xs">{{ $item->user->name }}</td>
                                    <td class="px-6 py-4 font-medium text-slate-800 text-xs">{{ $item->event_name }}</td>
                                    <td class="px-6 py-4">
                                        <div class="text-[10px] text-slate-400 font-bold uppercase">{{ $item->pilar_type }}</div>
                                        <div class="font-bold text-slate-900 text-xs">Rp {{ number_format($item->cost_amount, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-xs text-slate-600">{{ $item->penerima_manfaat ?? 0 }} Org</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($item->status == 'validated')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 border border-green-200">
                                                <i class="fas fa-check-circle mr-1"></i> DISETUJUI
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200">
                                                <i class="fas fa-times-circle mr-1"></i> DITOLAK
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-slate-400 italic text-sm">Belum ada riwayat approval.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('vite-scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Flatpickr
            const fp = flatpickr(".flatpickr-date", {
                mode: "range",
                dateFormat: "Y-m-d",
                static: true,
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        instance.element.closest('form').submit();
                    }
                }
            });

            @if(session('success'))
                Swal.fire({
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'rounded-2xl border-none shadow-xl',
                    }
                });
            @endif
        });

        function confirmAction(formId, title, text, icon, confirmText) {
            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: icon === 'success' ? '#15803d' : '#dc2626',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-3xl border-none shadow-2xl',
                    confirmButton: 'rounded-xl px-6 py-2.5 font-bold',
                    cancelButton: 'rounded-xl px-6 py-2.5 font-medium'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }

        function viewPhoto(url, title) {
            Swal.fire({
                title: title,
                imageUrl: url,
                imageAlt: 'Dokumentasi ' + title,
                showCloseButton: true,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-3xl border-none overflow-hidden',
                    image: 'rounded-2xl shadow-lg m-0',
                    title: 'pt-6 pb-2 text-slate-800'
                }
            });
        }
    </script>
    @endpush
@endsection