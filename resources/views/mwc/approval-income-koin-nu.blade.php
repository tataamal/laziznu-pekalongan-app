@extends('layouts.app')

@section('page_title', 'Persetujuan Input Koin NU')
@section('page_subtitle', 'Tinjau dan setujui laporan koin NU dari Ranting.')

@section('content')
    <div class="w-full space-y-8">
        {{-- Filter & Search Section --}}
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Global Search --}}
                <div class="space-y-2">
                    <label for="searchInput" class="text-xs font-semibold uppercase tracking-wider text-slate-500 ml-1">Cari Laporan</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input 
                            type="text" 
                            id="searchInput" 
                            placeholder="Cari kode, ranting, atau nominal..." 
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 py-3 pl-11 pr-4 text-sm transition focus:border-green-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-green-500/10"
                        >
                    </div>
                </div>

                {{-- Date Filter --}}
                <div class="space-y-2">
                    <label for="dateFilter" class="text-xs font-semibold uppercase tracking-wider text-slate-500 ml-1">Filter Tanggal</label>
                    <div class="relative">
                        <i class="fas fa-calendar-alt absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input 
                            type="text" 
                            id="dateFilter" 
                            placeholder="Pilih rentang tanggal..." 
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 py-3 pl-11 pr-4 text-sm transition focus:border-green-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-green-500/10"
                        >
                    </div>
                </div>

                {{-- Stats Summary (Quick Info) --}}
                <div class="flex items-center justify-end">
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
            </div>
        </div>

        {{-- Table Section --}}
        <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse" id="approvalTable">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Info Transaksi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Ranting / Pengaju</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Detail Dana (KOIN NU)</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Persentase & Budget</th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500 w-[200px]">Aksi Persetujuan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($items as $item)
                            <tr class="hover:bg-slate-50/50 transition-colors approval-row" 
                                data-search="{{ strtolower($item->transaction_code . ' ' . $item->user->name . ' ' . $item->net_income) }}"
                                data-date="{{ $item->date }}">
                                <td class="px-6 py-5">
                                    <div class="font-bold text-slate-900 leading-tight">{{ $item->transaction_code }}</div>
                                    <div class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                                        <i class="far fa-calendar-alt text-[10px]"></i>
                                        {{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-xl bg-green-50 flex items-center justify-center text-green-600 font-bold text-sm border border-green-100">
                                            {{ substr($item->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-800 text-sm">{{ $item->user->name }}</div>
                                            <div class="text-[10px] text-zinc-400 font-medium uppercase tracking-wider mt-0.5">Petugas Ranting</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="text-xs text-slate-500 mb-0.5">Total Bersih (Koin NU)</div>
                                    <div class="font-bold text-green-700">Rp {{ number_format($item->net_income, 0, ',', '.') }}</div>
                                    <div class="text-[10px] text-slate-400 mt-1">
                                        <span class="line-through">Gross: Rp {{ number_format($item->gross_profit, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="inline-flex items-center rounded-lg bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700 border border-blue-100">
                                            {{ $item->percentage }}%
                                        </span>
                                        <span class="text-[10px] font-medium text-slate-500 uppercase tracking-tighter">Budget Tersedia</span>
                                    </div>
                                    <div class="font-bold text-slate-800">Rp {{ number_format($item->allowed_budget, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Approve Button --}}
                                        <form action="{{ route('mwc.approval-income-koin-nu.approve', $item->id) }}" method="POST" id="approve-form-{{ $item->id }}">
                                            @csrf
                                            <button 
                                                type="button" 
                                                onclick="confirmAction('approve-form-{{ $item->id }}', 'Setujui Laporan?', 'Laporan ini akan divalidasi dan saldo akan ditambahkan ke Ranting.', 'success', 'Ya, Setujui')"
                                                class="h-10 w-10 flex items-center justify-center rounded-xl bg-green-600 text-white hover:bg-green-700 shadow-sm shadow-green-200 transition-all hover:scale-110"
                                                title="Setujui Laporan"
                                            >
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>

                                        {{-- Reject Button --}}
                                        <form action="{{ route('mwc.approval-income-koin-nu.reject', $item->id) }}" method="POST" id="reject-form-{{ $item->id }}">
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
                                <td colspan="5" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center opacity-40">
                                        <div class="h-20 w-20 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                            <i class="fas fa-inbox text-3xl text-slate-400"></i>
                                        </div>
                                        <p class="text-slate-500 font-medium tracking-tight">Tidak ada laporan yang menunggu persetujuan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('vite-scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const dateFilter = document.getElementById('dateFilter');
            const tableRows = document.querySelectorAll('.approval-row');

            // Initialize Flatpickr
            const fp = flatpickr("#dateFilter", {
                mode: "range",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d M Y",
                onChange: function(selectedDates, dateStr, instance) {
                    filterTable();
                }
            });

            function filterTable() {
                const query = searchInput.value.toLowerCase();
                const selectedDates = fp.selectedDates;
                
                tableRows.forEach(row => {
                    const searchData = row.getAttribute('data-search');
                    const rowDateStr = row.getAttribute('data-date');
                    const rowDate = new Date(rowDateStr);
                    rowDate.setHours(0,0,0,0);

                    const matchesSearch = searchData.includes(query);
                    
                    let matchesDate = true;
                    if (selectedDates.length === 1) {
                        const start = new Date(selectedDates[0]);
                        start.setHours(0,0,0,0);
                        matchesDate = rowDate.getTime() === start.getTime();
                    } else if (selectedDates.length === 2) {
                        const start = new Date(selectedDates[0]);
                        const end = new Date(selectedDates[1]);
                        start.setHours(0,0,0,0);
                        end.setHours(23,59,59,999);
                        matchesDate = rowDate >= start && rowDate <= end;
                    }

                    if (matchesSearch && matchesDate) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });
            }

            searchInput.addEventListener('input', filterTable);

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
    </script>
    @endpush
@endsection