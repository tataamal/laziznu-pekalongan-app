@extends('layouts.app')

@section('page_title', 'Data Transaksi Infaq MWC')
@section('page_subtitle', 'Kelola infaq yang masuk ke MWC.')

@section('content')
    <div class="w-full space-y-8">
        {{-- Header & Quick Stats --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-2xl bg-green-700 text-white flex items-center justify-center shadow-lg shadow-green-200">
                    <i class="fas fa-hand-holding-heart text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Riwayat Infaq MWC</h2>
                    <p class="text-sm text-slate-500">Total {{ $items->count() }} transaksi tercatat</p>
                </div>
            </div>
            
            <button 
                onclick="openCreateModal()"
                class="flex items-center justify-center gap-2 rounded-2xl bg-green-700 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-green-200 transition-all hover:bg-green-800 hover:scale-105 active:scale-95"
            >
                <i class="fas fa-plus"></i>
                <span>Catat Transaksi Baru</span>
            </button>
        </div>

        {{-- Filter & Search Section --}}
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Global Search --}}
                <div class="space-y-2">
                    <label for="searchInput" class="text-xs font-semibold uppercase tracking-wider text-slate-500 ml-1">Cari Transaksi</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input 
                            type="text" 
                            id="searchInput" 
                            placeholder="Cari kode, jenis, atau deskripsi..." 
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

                {{-- Bulk Action --}}
                <div class="flex items-end gap-3">
                    <button 
                        id="bulkDeleteBtn"
                        disabled
                        onclick="confirmBulkDelete()"
                        class="flex flex-1 items-center justify-center gap-2 rounded-2xl bg-red-50 px-4 py-3 text-sm font-bold text-red-600 border border-red-100 transition-all hover:bg-red-600 hover:text-white disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <i class="fas fa-trash-alt"></i>
                        <span>Hapus Terpilih (<span id="selectedCount">0</span>)</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden text-sm">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse" id="infaqTable">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th class="px-6 py-4 text-center w-12">
                                <input type="checkbox" id="selectAll" class="rounded border-slate-300 text-green-600 focus:ring-green-500">
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Info Transaksi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Jenis Transaksi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Keterangan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Jumlah Total</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Jumlah Bersih</th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500 w-[120px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($items as $item)
                            <tr class="hover:bg-slate-50/50 transition-colors infaq-row" 
                                data-search="{{ strtolower($item->transaction_code . ' ' . $item->infaq_type . ' ' . $item->description) }}"
                                data-date="{{ $item->transaction_date }}">
                                <td class="px-6 py-5 text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $item->id }}" class="row-checkbox rounded border-slate-300 text-green-600 focus:ring-green-500">
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-slate-900 leading-tight">{{ $item->transaction_code }}</div>
                                    <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1.5 uppercase font-medium">
                                        {{ \Carbon\Carbon::parse($item->transaction_date)->format('d M Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex items-center rounded-lg {{ $item->transaction_type === 'Pemasukan' ? 'bg-green-50 text-green-700 border-green-100' : 'bg-red-50 text-red-700 border-red-100' }} border px-2 py-0.5 text-[10px] font-bold mb-1 uppercase">
                                        {{ $item->transaction_type }}
                                    </span>
                                    <div class="font-semibold text-slate-800">{{ $item->infaq_type }}</div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="text-slate-500 max-w-[200px] truncate" title="{{ $item->description }}">
                                        {{ $item->description ?: '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-slate-900">Rp {{ number_format($item->gross_amount, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-green-700">Rp {{ number_format($item->net_amount, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-center gap-2">
                                        <button 
                                            onclick="openEditModal({{ json_encode($item) }})"
                                            class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-100 text-slate-600 hover:bg-green-700 hover:text-white transition-all shadow-sm border border-slate-200"
                                            title="Edit"
                                        >
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        <form action="{{ route('mwc.infaq-transaction.destroy', $item->id) }}" method="POST" id="delete-form-{{ $item->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="button" 
                                                onclick="confirmDelete('delete-form-{{ $item->id }}')"
                                                class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-100 text-slate-600 hover:bg-red-600 hover:text-white transition-all shadow-sm border border-slate-200"
                                                title="Hapus"
                                            >
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center opacity-40">
                                        <div class="h-20 w-20 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                            <i class="fas fa-history text-3xl text-slate-400"></i>
                                        </div>
                                        <p class="text-slate-500 font-medium tracking-tight">Belum ada transaksi infaq tercatat.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    <div id="infaqModal" class="fixed inset-0 z-[60] hidden overflow-y-auto bg-slate-900/40 backdrop-blur-sm transition-opacity duration-300">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="w-full max-w-xl scale-95 rounded-3xl bg-white p-8 shadow-2xl transition-all duration-300" id="modalContainer">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-900" id="modalTitle">Transmit Baru</h3>
                        <p class="text-sm text-slate-500">Isi detail transaksi infaq di bawah ini.</p>
                    </div>
                    <button onclick="closeModal()" class="h-10 w-10 rounded-2xl bg-slate-50 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-all flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="infaqForm" method="POST" class="space-y-6">
                    @csrf
                    <div id="methodField"></div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Tanggal --}}
                        <div class="space-y-2">
                            <label for="transaction_date" class="text-sm font-semibold text-slate-700 ml-1">Tanggal</label>
                            <input type="date" name="transaction_date" id="transaction_date" required class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm transition focus:border-green-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-green-500/10">
                        </div>

                        {{-- Tipe --}}
                        <div class="space-y-2">
                            <label for="transaction_type" class="text-sm font-semibold text-slate-700 ml-1">Tipe Transaksi</label>
                            <select name="transaction_type" id="transaction_type" required class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm transition focus:border-green-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-green-500/10">
                                <option value="Pemasukan">Pemasukan</option>
                                <option value="Pengeluaran">Pengeluaran</option>
                            </select>
                        </div>
                    </div>

                    {{-- Jenis Infaq --}}
                    <div class="space-y-2">
                        <label for="infaq_type" class="text-sm font-semibold text-slate-700 ml-1">Jenis Infaq</label>
                        <select name="infaq_type" id="infaq_type" required class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm transition focus:border-green-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-green-500/10">
                            <option value="" disabled selected>Pilih Jenis Infaq</option>
                            <option value="Infaq UMKM">Infaq UMKM</option>
                            <option value="Infaq Toko">Infaq Toko</option>
                            <option value="Infaq LP">Infaq LP</option>
                            <option value="Infaq Layanan">Infaq Layanan</option>
                            <option value="Infaq Lainnya">Infaq Lainnya</option>
                        </select>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="space-y-2">
                        <label for="description" class="text-sm font-semibold text-slate-700 ml-1">Keterangan (Opsional)</label>
                        <textarea name="description" id="description" rows="2" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm transition focus:border-green-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-green-500/10"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Gross --}}
                        <div class="space-y-2">
                            <label for="gross_amount" class="text-sm font-semibold text-slate-700 ml-1">Jumlah Total (Rp)</label>
                            <input type="number" name="gross_amount" id="gross_amount" required class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm transition focus:border-green-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-green-500/10">
                        </div>

                        {{-- Percentage --}}
                        <div class="space-y-2">
                            <label for="percentage" class="text-sm font-semibold text-slate-700 ml-1">Persentase (%)</label>
                            <div class="relative">
                                <input type="number" name="percentage" id="percentage" value="10" readonly class="w-full rounded-2xl border border-slate-200 bg-zinc-100 p-3.5 pr-10 text-sm font-bold text-slate-500 cursor-not-allowed outline-none">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">%</span>
                            </div>
                        </div>
                    </div>

                    {{-- Dynamic Result display --}}
                    <div class="p-4 rounded-2xl bg-green-50 border border-green-100 flex items-center justify-between">
                        <div>
                            <div class="text-[10px] font-bold text-green-600 uppercase tracking-wider mb-0.5">Jumlah Bersih (Jumlah Total - 10%)</div>
                            <div class="text-2xl font-black text-green-900" id="netDisplay">Rp 0</div>
                        </div>
                        <i class="fas fa-calculator text-2xl text-green-200"></i>
                    </div>

                    <button type="submit" class="w-full rounded-2xl bg-green-700 py-4 text-sm font-bold text-white shadow-lg shadow-green-200 transition-all hover:bg-green-800 hover:scale-[1.02] active:scale-100">
                        Simpan Transaksi
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Bulk Delete Form --}}
    <form id="bulkDeleteForm" action="{{ route('mwc.infaq-transaction.bulk-delete') }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
        <div id="bulkDeleteInputs"></div>
    </form>

    @push('vite-scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let modal, modalContainer, searchInput, tableRows, selectAll, rowCheckboxes, bulkDeleteBtn, selectedCountDisp, fp;

        document.addEventListener('DOMContentLoaded', function() {
            modal = document.getElementById('infaqModal');
            modalContainer = document.getElementById('modalContainer');
            searchInput = document.getElementById('searchInput');
            tableRows = document.querySelectorAll('.infaq-row');
            selectAll = document.getElementById('selectAll');
            rowCheckboxes = document.querySelectorAll('.row-checkbox');
            bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            selectedCountDisp = document.getElementById('selectedCount');

            // Flatpickr Init
            fp = flatpickr("#dateFilter", {
                mode: "range",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d M Y",
                onChange: function(selectedDates, dateStr, instance) {
                    filterTable();
                }
            });

            // Search & Filter Event
            if (searchInput) {
                searchInput.addEventListener('input', filterTable);
            }

            // Calculation Reactive Listeners
            const grossInput = document.getElementById('gross_amount');
            if (grossInput) grossInput.addEventListener('input', calculateNet);

            // Bulk Actions Events
            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    rowCheckboxes.forEach(cb => {
                        if (!cb.closest('tr').classList.contains('hidden')) {
                            cb.checked = this.checked;
                        }
                    });
                    updateBulkBtn();
                });
            }

            rowCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateBulkBtn);
            });
        });

        // Search & Filter
        function filterTable() {
            if (!searchInput || !fp) return;
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

        function updateBulkBtn() {
            const checkedCount = Array.from(rowCheckboxes).filter(cb => cb.checked).length;
            bulkDeleteBtn.disabled = checkedCount === 0;
            selectedCountDisp.textContent = checkedCount;
        }

        function confirmBulkDelete() {
            const checked = Array.from(rowCheckboxes).filter(cb => cb.checked);
            const ids = checked.map(cb => cb.value);
            
            Swal.fire({
                title: 'Hapus ' + ids.length + ' data?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus Semua!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-3xl border-none shadow-2xl' }
            }).then((result) => {
                if (result.isConfirmed) {
                    const inputContainer = document.getElementById('bulkDeleteInputs');
                    inputContainer.innerHTML = '';
                    ids.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = id;
                        inputContainer.appendChild(input);
                    });
                    document.getElementById('bulkDeleteForm').submit();
                }
            });
        }

        // Modal Logic
        function openCreateModal() {
            if (!modal) return;
            document.getElementById('modalTitle').textContent = 'Transmit Baru';
            document.getElementById('infaqForm').action = "{{ route('mwc.infaq-transaction.store') }}";
            document.getElementById('methodField').innerHTML = '';
            document.getElementById('infaqForm').reset();
            
            // Set Today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('transaction_date').value = today;
            document.getElementById('percentage').value = "10";
            
            calculateNet();
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContainer.classList.remove('scale-95', 'opacity-0');
            }, 10);
        }

        function openEditModal(item) {
            if (!modal) return;
            document.getElementById('modalTitle').textContent = 'Edit Transaksi';
            document.getElementById('infaqForm').action = "/mwc/infaq-transaction/" + item.id;
            document.getElementById('methodField').innerHTML = '@method("PUT")';
            
            document.getElementById('transaction_date').value = item.transaction_date;
            document.getElementById('transaction_type').value = item.transaction_type;
            document.getElementById('infaq_type').value = item.infaq_type;
            document.getElementById('description').value = item.description;
            document.getElementById('gross_amount').value = item.gross_amount;
            document.getElementById('percentage').value = "10";
            
            calculateNet();
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContainer.classList.remove('scale-95', 'opacity-0');
            }, 10);
        }

        function closeModal() {
            if (!modalContainer) return;
            modalContainer.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function calculateNet() {
            const grossInput = document.getElementById('gross_amount');
            if (!grossInput) return;

            const gross = parseFloat(grossInput.value) || 0;
            const percent = 10;
            
            // Formula: Gross - (Gross * 10%)
            const fee = (gross * percent) / 100;
            const net = Math.floor(gross - fee);
            
            document.getElementById('netDisplay').textContent = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(net);
        }

        function confirmDelete(formId) {
            Swal.fire({
                title: 'Hapus Data?',
                text: "Data ini akan dihapus secara permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-3xl border-none shadow-2xl' }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }

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
                customClass: { popup: 'rounded-2xl border-none shadow-xl' }
            });
        @endif
        
        @if($errors->any())
            Swal.fire({
                title: 'Error!',
                text: "{{ $errors->first() }}",
                icon: 'error',
                customClass: { popup: 'rounded-3xl border-none shadow-2xl' }
            });
        @endif
    </script>
    @endpush
@endsection