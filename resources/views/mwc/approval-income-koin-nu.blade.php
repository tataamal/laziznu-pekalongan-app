@extends('layouts.app')

@section('page_title', 'Approval Pemasukan Koin NU')
@section('page_subtitle', 'Tinjau dan setujui laporan pemasukan koin NU dari Ranting.')

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
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Ranting Asal</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Pemasukan Bersih</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Dana Dapat Digunakan</th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500 w-[200px]">Aksi</th>
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
                                        <div>
                                            <div class="font-semibold text-slate-800 text-sm">{{ $item->user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-green-700">Rp {{ number_format($item->net_income, 0, ',', '.') }}</div>
                                    <div class="text-[10px] text-slate-400 mt-1">
                                        <span c>Pemasukan Total: Rp {{ number_format($item->gross_profit, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
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
                                                onclick="confirmAction('reject-form-{{ $item->id }}', 'Tolak Laporan?', 'Yakin ingin menolak laporan ini? Laporan yang ditolak tidak akan dihitung ke dalam saldo.', 'error', 'Ya, Tolak')"
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
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Pemasukan Bersih</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Dana Digunakan</th>
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
                                    <td class="px-6 py-4 font-bold text-green-700 text-xs">Rp {{ number_format($item->net_income, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 font-medium text-slate-800 text-xs">Rp {{ number_format($item->allowed_budget, 0, ',', '.') }}</td>
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
                                    <td colspan="5" class="px-6 py-10 text-center text-slate-400 italic text-sm">Belum ada riwayat approval.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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