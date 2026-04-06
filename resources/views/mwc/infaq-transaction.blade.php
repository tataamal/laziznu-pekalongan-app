@extends('layouts.app')
@section('page_title', 'Data Transaksi Infaq MWC')
@section('page_subtitle', 'Kelola infaq yang masuk ke MWC.')

@section('content')
    <div class="w-full space-y-8">
        {{-- Header & Quick Stats --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="h-12 w-12 rounded-2xl bg-green-700 text-white flex items-center justify-center shadow-lg shadow-green-200">
                    <i class="fas fa-hand-holding-heart text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Riwayat Infaq MWC</h2>
                    <p class="text-sm text-slate-500">Total {{ $items->count() }} transaksi tercatat</p>
                </div>
            </div>

            <button onclick="openCreateModal()"
                class="flex items-center justify-center gap-2 rounded-2xl bg-green-700 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-green-200 transition-all hover:bg-green-800 hover:scale-105 active:scale-95">
                <i class="fas fa-plus"></i>
                <span>Catat Transaksi Baru</span>
            </button>
        </div>

        {{-- Filter & Search Section --}}
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label for="searchInput" class="text-xs font-semibold uppercase tracking-wider text-slate-500 ml-1">Cari
                        Transaksi</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" id="searchInput" placeholder="Cari kode, jenis, atau deskripsi..."
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 py-3 pl-11 pr-4 text-sm transition focus:border-green-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-green-500/10">
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="dateFilter"
                        class="text-xs font-semibold uppercase tracking-wider text-slate-500 ml-1">Filter Tanggal</label>
                    <div class="relative">
                        <i class="fas fa-calendar-alt absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" id="dateFilter" placeholder="Pilih rentang tanggal..."
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 py-3 pl-11 pr-4 text-sm transition focus:border-green-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-green-500/10">
                    </div>
                </div>

                <div class="flex items-end gap-3">
                    <button id="bulkDeleteBtn" disabled onclick="confirmBulkDelete()"
                        class="flex flex-1 items-center justify-center gap-2 rounded-2xl bg-red-50 px-4 py-3 text-sm font-bold text-red-600 border border-red-100 transition-all hover:bg-red-600 hover:text-white disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-trash-alt"></i>
                        <span>Hapus Terpilih (<span id="selectedCount">0</span>)</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Table Pemasukan Section --}}
        <div class="mb-4 mt-6 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800 ml-2">Data Pemasukan</h3>
        </div>
        <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden text-sm mb-8">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th class="px-6 py-4 text-center w-12">
                                <input type="checkbox" class="select-all-cb rounded border-slate-300 text-green-600 focus:ring-green-500">
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Info Transaksi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Jenis</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Keterangan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Total</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Bersih</th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500 w-[120px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($items->where('transaction_type', 'Pemasukan') as $item)
                            <tr class="hover:bg-slate-50/50 transition-colors infaq-row"
                                data-search="{{ strtolower($item->transaction_code . ' ' . $item->infaq_type . ' ' . $item->description) }}"
                                data-date="{{ $item->transaction_date }}">
                                <td class="px-6 py-5 text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $item->id }}"
                                        class="row-checkbox rounded border-slate-300 text-green-600 focus:ring-green-500">
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-slate-900 leading-tight">{{ $item->transaction_code }}</div>
                                    <div class="text-[11px] text-slate-500 mt-1 uppercase font-medium">
                                        {{ \Carbon\Carbon::parse($item->transaction_date)->format('d M Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex items-center rounded-lg bg-green-50 text-green-700 border-green-100 border px-2 py-0.5 text-[10px] font-bold mb-1 uppercase">
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
                                <td class="px-6 py-5 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openEditModal({{ json_encode($item) }})" class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-100 text-slate-600 hover:bg-green-700 hover:text-white transition-all shadow-sm border border-slate-200">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        <form action="{{ route('mwc.infaq-transaction.destroy', $item->id) }}" method="POST" id="delete-form-{{ $item->id }}">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDelete('delete-form-{{ $item->id }}')" class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-100 text-slate-600 hover:bg-red-600 hover:text-white transition-all shadow-sm border border-slate-200">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-20 text-center text-slate-400">Belum ada transaksi pemasukan tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Table Pengeluaran Section --}}
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800 ml-2">Data Pengeluaran</h3>
        </div>
        <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden text-sm">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th class="px-6 py-4 text-center w-12">
                                <input type="checkbox" class="select-all-cb rounded border-slate-300 text-green-600 focus:ring-green-500">
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Info Transaksi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Jenis</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Keterangan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Penerima</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Total</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Bersih</th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500 w-[120px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($items->where('transaction_type', 'Pengeluaran') as $item)
                            <tr class="hover:bg-slate-50/50 transition-colors infaq-row"
                                data-search="{{ strtolower($item->transaction_code . ' ' . $item->infaq_type . ' ' . $item->description) }}"
                                data-date="{{ $item->transaction_date }}">
                                <td class="px-6 py-5 text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $item->id }}"
                                        class="row-checkbox rounded border-slate-300 text-green-600 focus:ring-green-500">
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-slate-900 leading-tight">{{ $item->transaction_code }}</div>
                                    <div class="text-[11px] text-slate-500 mt-1 uppercase font-medium">
                                        {{ \Carbon\Carbon::parse($item->transaction_date)->format('d M Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex items-center rounded-lg bg-red-50 text-red-700 border-red-100 border px-2 py-0.5 text-[10px] font-bold mb-1 uppercase">
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
                                    <div class="font-bold text-slate-900">{{ number_format($item->penerima_manfaat, 0, ',', '.') }}</div>
                                    <div class="text-[10px] text-slate-500 uppercase font-medium">Jiwa</div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-slate-900">Rp {{ number_format($item->gross_amount, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-green-700">Rp {{ number_format($item->net_amount, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openEditModal({{ json_encode($item) }})" class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-100 text-slate-600 hover:bg-green-700 hover:text-white transition-all shadow-sm border border-slate-200">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        <form action="{{ route('mwc.infaq-transaction.destroy', $item->id) }}" method="POST" id="delete-form-{{ $item->id }}">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDelete('delete-form-{{ $item->id }}')" class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-100 text-slate-600 hover:bg-red-600 hover:text-white transition-all shadow-sm border border-slate-200">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-20 text-center text-slate-400">Belum ada transaksi pengeluaran tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div id="infaqModal"
        class="fixed inset-0 z-[60] hidden overflow-y-auto bg-slate-900/40 backdrop-blur-sm transition-opacity duration-300">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="w-full max-w-xl scale-95 rounded-3xl bg-white p-8 shadow-2xl transition-all duration-300"
                id="modalContainer">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-900" id="modalTitle">Transaksi Baru</h3>
                        <p class="text-sm text-slate-500">Isi detail transaksi infaq di bawah ini.</p>
                    </div>
                    <button onclick="closeModal()"
                        class="h-10 w-10 rounded-2xl bg-slate-50 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-all flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="infaqForm" method="POST" class="space-y-6">
                    @csrf
                    <div id="methodField"></div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label for="transaction_date"
                                class="text-sm font-semibold text-slate-700 ml-1">Tanggal</label>
                            <div class="relative">
                                <i
                                    class="fas fa-calendar-alt absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input type="text" name="transaction_date" id="transaction_date" required
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 py-3.5 pl-11 pr-4 text-sm focus:border-green-500 focus:outline-none focus:ring-4 focus:ring-green-500/10">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label for="transaction_type" class="text-sm font-semibold text-slate-700 ml-1">Tipe
                                Transaksi</label>
                            <select name="transaction_type" id="transaction_type" required onchange="handleTypeChange()"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm focus:border-green-500 focus:outline-none focus:ring-4 focus:ring-green-500/10">
                                <option value="Pemasukan">Pemasukan</option>
                                <option value="Pengeluaran">Pengeluaran</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="infaq_type" class="text-sm font-semibold text-slate-700 ml-1">Jenis Infaq</label>
                        <select name="infaq_type" id="infaq_type" required
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm focus:border-green-500 focus:outline-none focus:ring-4 focus:ring-green-500/10">
                        </select>
                    </div>

                    {{-- Jenis Pilar (Akan di-hide jika Pemasukan) --}}
                    <div class="space-y-2" id="pilarContainer">
                        <label for="pilar_type" class="text-sm font-semibold text-slate-700 ml-1">Jenis Pilar</label>
                        <select name="pilar_type" id="pilar_type" required
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm focus:border-green-500 focus:outline-none focus:ring-4 focus:ring-green-500/10">
                            <option value="" disabled selected>Pilih Jenis Pilar</option>
                            <option value="Nu Cerdas">Nu Care - Cerdas</option>
                            <option value="Nu Sehat">Nu Care - Sehat</option>
                            <option value="Nu Berdaya">Nu Care - Berdaya</option>
                            <option value="Nu Hijau">Nu Care - Hijau</option>
                            <option value="Nu Damai">Nu Care - Damai</option>
                        </select>
                    </div>

                    {{-- Penerima Manfaat (Akan di-hide jika Pemasukan) --}}
                    <div class="space-y-2 transition-all duration-300" id="penerimaContainer">
                        <label for="penerima_manfaat" class="text-sm font-semibold text-slate-700 ml-1">Jumlah Penerima
                            Manfaat (Jiwa)</label>
                        <input type="number" name="penerima_manfaat" id="penerima_manfaat" min="0"
                            value="0"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm focus:border-green-500 focus:outline-none focus:ring-4 focus:ring-green-500/10">
                    </div>

                    <div class="space-y-2">
                        <label for="description" class="text-sm font-semibold text-slate-700 ml-1">Keterangan
                            (Opsional)</label>
                        <textarea name="description" id="description" rows="2"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm focus:border-green-500 focus:outline-none focus:ring-4 focus:ring-green-500/10"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label for="gross_amount" class="text-sm font-semibold text-slate-700 ml-1">Jumlah Total
                                (Rp)</label>
                            <input type="number" name="gross_amount" id="gross_amount" required
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm focus:border-green-500 focus:outline-none focus:ring-4 focus:ring-green-500/10">
                        </div>
                        <div class="space-y-2" id="percentageContainer">
                            <label for="percentage" class="text-sm font-semibold text-slate-700 ml-1">Persentase
                                (%)</label>
                            <div class="relative">
                                <input type="number" id="percentage" value="10" readonly
                                    class="w-full rounded-2xl border border-slate-200 bg-zinc-100 p-3.5 pr-10 text-sm font-bold text-slate-500 cursor-not-allowed">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">%</span>
                            </div>
                        </div>
                    </div>

                    <div id="netDisplayContainer"
                        class="p-4 rounded-2xl bg-green-50 border border-green-100 flex items-center justify-between">
                        <div>
                            <div class="text-[10px] font-bold text-green-600 uppercase tracking-wider mb-0.5">Jumlah Bersih
                                (-10%)</div>
                            <div class="text-2xl font-black text-green-900" id="netDisplay">Rp 0</div>
                        </div>
                        <i class="fas fa-calculator text-2xl text-green-200"></i>
                    </div>

                    <button type="submit"
                        class="w-full rounded-2xl bg-green-700 py-4 text-sm font-bold text-white shadow-lg shadow-green-200 transition-all hover:bg-green-800 hover:scale-[1.02] active:scale-100">
                        Simpan Transaksi
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('vite-scripts')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            let modal, searchInput, tableRows, selectAll, rowCheckboxes, bulkDeleteBtn, fpFilter, fpModal;

            document.addEventListener('DOMContentLoaded', function() {
                modal = document.getElementById('infaqModal');
                searchInput = document.getElementById('searchInput');
                tableRows = document.querySelectorAll('.infaq-row');
                // Not used

                rowCheckboxes = document.querySelectorAll('.row-checkbox');
                bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

                // Flatpickr untuk Filter Tanggal
                fpFilter = flatpickr("#dateFilter", {
                    mode: "range",
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d M Y",
                    onChange: filterTable
                });

                // Flatpickr untuk Input di Modal
                fpModal = flatpickr("#transaction_date", {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d M Y",
                    allowInput: true,
                    static: true
                });

                if (searchInput) searchInput.addEventListener('input', filterTable);

                const grossInput = document.getElementById('gross_amount');
                if (grossInput) grossInput.addEventListener('input', calculateNet);

                const selectAllBoxes = document.querySelectorAll('.select-all-cb');
                selectAllBoxes.forEach(box => {
                    box.addEventListener('change', function() {
                        const table = this.closest('table');
                        const checkboxes = table.querySelectorAll('.row-checkbox');
                        checkboxes.forEach(cb => {
                            if (!cb.closest('tr').classList.contains('hidden')) cb.checked = this.checked;
                        });
                        updateBulkBtn();
                    });
                });
                rowCheckboxes.forEach(cb => cb.addEventListener('change', updateBulkBtn));
            });

            function handleTypeChange() {
                const type = document.getElementById('transaction_type').value;
                const pilarContainer = document.getElementById('pilarContainer');
                const pilarSelect = document.getElementById('pilar_type');
                const container = document.getElementById('penerimaContainer');
                const input = document.getElementById('penerima_manfaat');
                const infaqTypeSelect = document.getElementById('infaq_type');
                const percentageContainer = document.getElementById('percentageContainer');
                const netDisplayContainer = document.getElementById('netDisplayContainer');

                if (type === 'Pemasukan') {
                    // Sembunyikan Pilar & Penerima jika Pemasukan
                    pilarContainer.classList.add('hidden');
                    pilarSelect.removeAttribute('required');

                    percentageContainer.classList.remove('hidden');
                    netDisplayContainer.classList.remove('hidden');
                    container.classList.add('hidden');
                    input.value = 0;
                } else {
                    // Tampilkan Pilar & Penerima jika Pengeluaran
                    pilarContainer.classList.remove('hidden');
                    pilarSelect.setAttribute('required', 'required');

                    percentageContainer.classList.add('hidden');
                    netDisplayContainer.classList.add('hidden');
                    container.classList.remove('hidden');
                }

                // Update opsi Jenis Infaq berdasarkan tipe
                let options = '<option value="" disabled selected>Pilih Jenis Infaq</option>';
                if (type === 'Pengeluaran') {
                    options += `
                        <option value="Saldo Koin NU">Saldo Koin NU</option>
                        <option value="Infaq Lain">Infaq Lain</option>`;
                } else {
                    options += `
                        <option value="Infaq UMKM">Infaq UMKM</option>
                        <option value="Infaq Toko">Infaq Toko</option>
                        <option value="Infaq LP">Infaq LP</option>
                        <option value="Infaq Layanan">Infaq Layanan</option>
                        <option value="Infaq Lainnya">Infaq Lainnya</option>`;
                }
                infaqTypeSelect.innerHTML = options;
            }

            function filterTable() {
                const query = searchInput.value.toLowerCase();
                const selectedDates = fpFilter.selectedDates;
                tableRows.forEach(row => {
                    const text = row.getAttribute('data-search');
                    const date = new Date(row.getAttribute('data-date')).setHours(0, 0, 0, 0);
                    let matchDate = true;
                    if (selectedDates.length === 2) {
                        matchDate = date >= selectedDates[0].setHours(0, 0, 0, 0) && date <= selectedDates[1].setHours(
                            23, 59, 59, 999);
                    }
                    row.classList.toggle('hidden', !(text.includes(query) && matchDate));
                });
            }

            function updateBulkBtn() {
                const checkedCount = Array.from(rowCheckboxes).filter(cb => cb.checked).length;
                bulkDeleteBtn.disabled = checkedCount === 0;
                document.getElementById('selectedCount').textContent = checkedCount;
            }

            function openCreateModal() {
                document.getElementById('modalTitle').textContent = 'Transaksi Baru';
                document.getElementById('infaqForm').action = "{{ route('mwc.infaq-transaction.store') }}";
                document.getElementById('methodField').innerHTML = '';
                document.getElementById('infaqForm').reset();

                fpModal.setDate(new Date()); // Set hari ini

                handleTypeChange();
                calculateNet();
                modal.classList.remove('hidden');
            }

            function openEditModal(item) {
                document.getElementById('modalTitle').textContent = 'Edit Transaksi';
                document.getElementById('infaqForm').action = "/mwc/infaq-transaction/" + item.id;
                document.getElementById('methodField').innerHTML = '@method('PUT')';

                fpModal.setDate(item.transaction_date);

                document.getElementById('transaction_type').value = item.transaction_type;
                handleTypeChange();

                document.getElementById('infaq_type').value = item.infaq_type;
                document.getElementById('pilar_type').value = item.pilar_type || "";
                document.getElementById('penerima_manfaat').value = item.penerima_manfaat;
                document.getElementById('description').value = item.description;
                document.getElementById('gross_amount').value = item.gross_amount;

                calculateNet();
                modal.classList.remove('hidden');
            }

            function closeModal() {
                modal.classList.add('hidden');
            }

            function calculateNet() {
                const gross = parseFloat(document.getElementById('gross_amount').value) || 0;
                const type = document.getElementById('transaction_type').value;
                const net = type === 'Pemasukan' ? Math.floor(gross - (gross * 0.1)) : gross;

                document.getElementById('netDisplay').textContent = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    maximumFractionDigits: 0
                }).format(net);
            }

            function confirmDelete(id) {
                Swal.fire({
                    title: 'Hapus?',
                    text: "Data akan hilang selamanya.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Ya, Hapus!'
                }).then(res => {
                    if (res.isConfirmed) document.getElementById(id).submit();
                });
            }
        </script>
    @endpush
@endsection
