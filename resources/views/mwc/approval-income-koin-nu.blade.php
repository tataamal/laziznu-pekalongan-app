@extends('layouts.app')

@section('page_title', 'Approval Pemasukan Koin NU')
@section('page_subtitle', 'Tinjau dan setujui laporan pemasukan koin NU dari Ranting.')

@section('content')
    <div class="w-full space-y-8">
        {{-- Filter & Search Section --}}
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <h3 class="text-lg font-bold text-slate-800">Filter Data</h3>
                {{-- Stats Summary (Quick Info) --}}
                <div class="px-6 py-3 bg-amber-50 rounded-2xl border border-amber-100 flex items-center gap-4">
                    <div class="h-10 w-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase text-amber-500 tracking-tight">Menunggu Persetujuan</div>
                        <div class="text-xl font-bold text-amber-900 leading-none">{{ $requestApproval->count() }} <span class="text-xs font-medium text-amber-700/60 uppercase ml-1">Laporan</span></div>
                    </div>
                </div>
            </div>

            <form action="{{ route('mwc.approval-income-koin-nu') }}" method="GET">
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
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
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
                    <a href="{{ route('mwc.approval-income-koin-nu') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-200 transition">Reset</a>
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
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Kode Transaksi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Ranting</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Jumlah Kaleng</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Pemasukan Koin NU</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Jasa Petugas</th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500 w-[240px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($requestApproval as $item)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-5">
                                    <div class="font-bold text-sm text-slate-900 leading-tight">{{ $item->transaction_code }}</div>
                                    <div class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                                        <i class="far fa-calendar-alt text-[10px]"></i>
                                        {{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div>
                                            <div class="font-semibold text-slate-800 text-sm">{{ $item->ranting?->nama_ranting ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-sm text-slate-800">{{ $item->jumlah_kaleng_aktif ?? 0 }} Kaleng Aktif</div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-sm text-green-700">Rp {{ number_format($item->pemasukan_koin_nu_bersih, 0, ',', '.') }}</div>
                                    <div class="text-xs text-slate-400 mt-1">
                                        <span>Pemasukan Total: Rp {{ number_format($item->pemasukan_koin_nu_kotor, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-sm text-slate-800">Rp {{ number_format($item->jasa_petugas, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- View Detail Button --}}
                                        <button 
                                            type="button" 
                                            onclick="openDetailModal({
                                                transaction_code: '{{ $item->transaction_code }}',
                                                date: '{{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}',
                                                koin_nu_ranting: {{ $item->koin_nu_ranting }},
                                                koin_nu_mwc: {{ $item->koin_nu_mwc }},
                                                koin_nu_pc: {{ $item->koin_nu_pc }},
                                                hak_amil_ranting: {{ $item->hak_amil_ranting }},
                                                hak_amil_mwc: {{ $item->hak_amil_mwc }},
                                                hak_amil_pc: {{ $item->hak_amil_pc }},
                                                dana_dapat_digunakan_ranting: {{ $item->dana_dapat_digunakan_ranting }},
                                                dana_dapat_digunakan_mwc: {{ $item->dana_dapat_digunakan_mwc }},
                                                dana_dapat_digunakan_pc: {{ $item->dana_dapat_digunakan_pc }}
                                            })"
                                            class="h-10 w-10 flex items-center justify-center rounded-xl bg-blue-600 text-white hover:bg-blue-700 shadow-sm shadow-blue-200 transition-all hover:scale-110"
                                            title="Lihat Detail"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        {{-- Approve Button --}}
                                        <form action="{{ route('mwc.approval-income-koin-nu.approve', $item->id) }}" method="POST" id="approve-form-{{ $item->id }}" class="inline">
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
                                        <form action="{{ route('mwc.approval-income-koin-nu.reject', $item->id) }}" method="POST" id="reject-form-{{ $item->id }}" class="inline">
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
                                <td colspan="6" class="px-6 py-20 text-center">
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
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Kode Transaksi</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Ranting</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Jumlah Kaleng</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Pemasukan Koin NU</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Jasa Petugas</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500">Status</th>
                                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($historyApproval as $item)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900 text-sm">{{ $item->transaction_code }}</div>
                                        <div class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                                            <i class="far fa-calendar-alt text-[10px]"></i>
                                            {{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-800 text-sm">{{ $item->ranting?->nama_ranting ?? ($item->user->name ?? '-') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-800 text-sm">{{ $item->jumlah_kaleng ?? 0 }} Kaleng</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-sm text-green-700">Rp {{ number_format($item->pemasukan_koin_nu_bersih ?? 0, 0, ',', '.') }}</div>
                                        <div class="text-xs text-slate-400 mt-1">Rp {{ number_format($item->pemasukan_koin_nu_kotor ?? 0, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-sm text-slate-800">Rp {{ number_format($item->jasa_petugas ?? 0, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($item->status == 'approved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 border border-green-200">
                                                <i class="fas fa-check-circle mr-1"></i> DISETUJUI
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200">
                                                <i class="fas fa-times-circle mr-1"></i> DITOLAK
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button
                                            type="button"
                                            onclick="openDetailModal({
                                                transaction_code: '{{ $item->transaction_code }}',
                                                date: '{{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}',
                                                koin_nu_ranting: {{ $item->koin_nu_ranting ?? 0 }},
                                                koin_nu_mwc: {{ $item->koin_nu_mwc ?? 0 }},
                                                koin_nu_pc: {{ $item->koin_nu_pc ?? 0 }},
                                                hak_amil_ranting: {{ $item->hak_amil_ranting ?? 0 }},
                                                hak_amil_mwc: {{ $item->hak_amil_mwc ?? 0 }},
                                                hak_amil_pc: {{ $item->hak_amil_pc ?? 0 }},
                                                dana_dapat_digunakan_ranting: {{ $item->dana_dapat_digunakan_ranting ?? 0 }},
                                                dana_dapat_digunakan_mwc: {{ $item->dana_dapat_digunakan_mwc ?? 0 }},
                                                dana_dapat_digunakan_pc: {{ $item->dana_dapat_digunakan_pc ?? 0 }}
                                            })"
                                            class="h-10 w-10 inline-flex items-center justify-center rounded-xl bg-blue-600 text-white hover:bg-blue-700 shadow-sm shadow-blue-200 transition-all hover:scale-110"
                                            title="Lihat Detail"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-slate-400 italic text-sm">Belum ada riwayat approval.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Modal --}}
    <div id="detailModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            {{-- Modal Header --}}
            <div class="sticky top-0 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-green-100 px-6 py-4 flex items-center justify-between z-10">
                <div>
                    <h3 class="text-xl font-bold text-slate-900">Detail Koin NU</h3>
                    <p class="text-sm text-slate-500 mt-1" id="modalTransactionCode">-</p>
                </div>
                <button onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 transition p-2">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            {{-- Modal Content --}}
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Row 1: Koin NU --}}
                    <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-3">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 mb-1">Koin NU Ranting</div>
                        <div class="text-lg font-bold text-emerald-900 truncate" id="modalKoinNuRanting">-</div>
                    </div>
                    <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-3">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 mb-1">Koin NU MWC</div>
                        <div class="text-lg font-bold text-emerald-900 truncate" id="modalKoinNuMwc">-</div>
                    </div>
                    <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-3">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 mb-1">Koin NU PC</div>
                        <div class="text-lg font-bold text-emerald-900 truncate" id="modalKoinNuPc">-</div>
                    </div>

                    {{-- Row 2: Hak Amil --}}
                    <div class="bg-orange-50 border border-orange-100 rounded-xl p-3">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-orange-600 mb-1">Hak Amil Ranting</div>
                        <div class="text-lg font-bold text-orange-900 truncate" id="modalHakAmilRanting">-</div>
                    </div>
                    <div class="bg-orange-50 border border-orange-100 rounded-xl p-3">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-orange-600 mb-1">Hak Amil MWC</div>
                        <div class="text-lg font-bold text-orange-900 truncate" id="modalHakAmilMwc">-</div>
                    </div>
                    <div class="bg-orange-50 border border-orange-100 rounded-xl p-3">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-orange-600 mb-1">Hak Amil PC</div>
                        <div class="text-lg font-bold text-orange-900 truncate" id="modalHakAmilPc">-</div>
                    </div>

                    {{-- Row 3: Dana Dapat Digunakan --}}
                    <div class="bg-violet-50 border border-violet-100 rounded-xl p-3">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-violet-600 mb-1">Dana Ranting</div>
                        <div class="text-lg font-bold text-violet-900 truncate" id="modalDanaDapatDigunakanRanting">-</div>
                    </div>
                    <div class="bg-violet-50 border border-violet-100 rounded-xl p-3">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-violet-600 mb-1">Dana MWC</div>
                        <div class="text-lg font-bold text-violet-900 truncate" id="modalDanaDapatDigunakanMwc">-</div>
                    </div>
                    <div class="bg-violet-50 border border-violet-100 rounded-xl p-3">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-violet-600 mb-1">Dana PC</div>
                        <div class="text-lg font-bold text-violet-900 truncate" id="modalDanaDapatDigunakanPc">-</div>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="sticky bottom-0 bg-slate-50 border-t border-slate-100 px-8 py-4 flex justify-end">
                <button onclick="closeDetailModal()" class="px-6 py-3 bg-slate-600 text-white font-semibold rounded-xl hover:bg-slate-700 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    @push('vite-scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        function openDetailModal(data) {
            document.getElementById('modalTransactionCode').textContent = data.transaction_code + ' • ' + data.date;
            document.getElementById('modalKoinNuRanting').textContent = 'Rp ' + formatCurrency(data.koin_nu_ranting);
            document.getElementById('modalKoinNuMwc').textContent = 'Rp ' + formatCurrency(data.koin_nu_mwc);
            document.getElementById('modalKoinNuPc').textContent = 'Rp ' + formatCurrency(data.koin_nu_pc);
            document.getElementById('modalHakAmilRanting').textContent = 'Rp ' + formatCurrency(data.hak_amil_ranting);
            document.getElementById('modalHakAmilMwc').textContent = 'Rp ' + formatCurrency(data.hak_amil_mwc);
            document.getElementById('modalHakAmilPc').textContent = 'Rp ' + formatCurrency(data.hak_amil_pc);
            document.getElementById('modalDanaDapatDigunakanRanting').textContent = 'Rp ' + formatCurrency(data.dana_dapat_digunakan_ranting);
            document.getElementById('modalDanaDapatDigunakanMwc').textContent = 'Rp ' + formatCurrency(data.dana_dapat_digunakan_mwc);
            document.getElementById('modalDanaDapatDigunakanPc').textContent = 'Rp ' + formatCurrency(data.dana_dapat_digunakan_pc);
            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        function formatCurrency(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Close modal when clicking outside
            const detailModal = document.getElementById('detailModal');
            if (detailModal) {
                detailModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeDetailModal();
                    }
                });
            }

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
    </script>
    @endpush
@endsection