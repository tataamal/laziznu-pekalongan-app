@extends('layouts.app')

@section('page_title', 'Data Transaksi Infaq PC')
@section('page_subtitle', 'Kelola infaq yang masuk ke Pimpinan Cabang.')

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
                    <h2 class="text-xl font-bold text-slate-900">Riwayat Infaq PC</h2>
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

        {{-- Table Section --}}
        <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden text-sm">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse" id="infaqTable">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th class="px-6 py-4 text-center w-12">
                                <input type="checkbox" id="selectAll"
                                    class="rounded border-slate-300 text-green-600 focus:ring-green-500">
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Info
                                Transaksi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Jenis
                                Transaksi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Keterangan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Penerima</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Jumlah
                                Total</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Jumlah
                                Bersih</th>
                            <th
                                class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500 w-[120px]">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($items as $item)
                            <tr class="hover:bg-slate-50/50 transition-colors infaq-row"
                                data-search="{{ strtolower($item->transaction_code . ' ' . $item->infaq_type . ' ' . $item->description) }}"
                                data-date="{{ $item->transaction_date }}">
                                <td class="px-6 py-5 text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $item->id }}"
                                        class="row-checkbox rounded border-slate-300 text-green-600 focus:ring-green-500">
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-slate-900 leading-tight">{{ $item->transaction_code }}</div>
                                    <div
                                        class="text-[11px] text-slate-500 mt-1 flex items-center gap-1.5 uppercase font-medium">
                                        {{ \Carbon\Carbon::parse($item->transaction_date)->format('d M Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span
                                        class="inline-flex items-center rounded-lg {{ $item->transaction_type === 'Pemasukan' ? 'bg-green-50 text-green-700 border-green-100' : 'bg-red-50 text-red-700 border-red-100' }} border px-2 py-0.5 text-[10px] font-bold mb-1 uppercase">
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
                                    <div class="font-bold text-slate-900">
                                        {{ number_format($item->penerima_manfaat, 0, ',', '.') }}</div>
                                    <div class="text-[10px] text-slate-500 uppercase font-medium">Jiwa</div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-slate-900">Rp
                                        {{ number_format($item->gross_amount, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-green-700">
                                        {{ $item->transaction_type === 'Pemasukan' ? 'Rp ' . number_format($item->net_amount, 0, ',', '.') : '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openEditModal({{ json_encode($item) }})"
                                            class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-100 text-slate-600 hover:bg-green-700 hover:text-white transition-all shadow-sm border border-slate-200">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        <form action="{{ route('pc.infaq.destroy', $item->id) }}" method="POST"
                                            id="delete-form-{{ $item->id }}">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                onclick="confirmDelete('delete-form-{{ $item->id }}')"
                                                class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-100 text-slate-600 hover:bg-red-600 hover:text-white transition-all shadow-sm border border-slate-200">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-20 text-center">
                                    <p class="text-slate-500 font-medium">Belum ada transaksi infaq tercatat.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    <div id="infaqModal"
        class="fixed inset-0 z-[60] hidden overflow-y-auto bg-slate-900/40 backdrop-blur-sm transition-opacity duration-300">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="w-full max-w-xl scale-95 rounded-3xl bg-white p-8 shadow-2xl transition-all duration-300 opacity-0"
                id="modalContainer">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-900" id="modalTitle">Transmit Baru</h3>
                        <p class="text-sm text-slate-500">Isi detail transaksi infaq di bawah ini.</p>
                    </div>
                    <button onclick="closeModal()"
                        class="h-10 w-10 rounded-2xl bg-slate-50 text-slate-400 hover:bg-slate-100 flex items-center justify-center">
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
                            <input type="date" name="transaction_date" id="transaction_date" required
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm focus:border-green-500 focus:outline-none focus:ring-4 focus:ring-green-500/10">
                        </div>

                        <div class="space-y-2">
                            <label for="transaction_type" class="text-sm font-semibold text-slate-700 ml-1">Tipe
                                Transaksi</label>
                            <select name="transaction_type" id="transaction_type" required
                                onchange="toggleTransactionFields()"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm focus:border-green-500 focus:outline-none focus:ring-4 focus:ring-green-500/10">
                                <option value="Pemasukan">Pemasukan</option>
                                <option value="Pengeluaran">Pengeluaran</option>
                            </select>
                        </div>
                    </div>

                    {{-- Jenis Infaq (Isi Pilihan via JS) --}}
                    <div class="space-y-2">
                        <label for="infaq_type" class="text-sm font-semibold text-slate-700 ml-1">Jenis Infaq</label>
                        <select name="infaq_type" id="infaq_type" required
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm focus:border-green-500 focus:outline-none focus:ring-4 focus:ring-green-500/10">
                        </select>
                    </div>

                    {{-- Jenis Pilar (Muncul jika Pengeluaran) --}}
                    <div class="space-y-2 hidden" id="pilarContainer">
                        <label for="pilar_type" class="text-sm font-semibold text-slate-700 ml-1">Jenis Pilar</label>
                        <select name="pilar_type" id="pilar_type"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm focus:border-green-500 focus:outline-none focus:ring-4 focus:ring-green-500/10">
                            <option value="Nu Care - Cerdas">Nu Care - Cerdas</option>
                            <option value="Nu Care - Sehat">Nu Care - Sehat</option>
                            <option value="Nu Care - Berdaya">Nu Care - Berdaya</option>
                            <option value="Nu Care - Hijau">Nu Care - Hijau</option>
                            <option value="Nu Care - Damai">Nu Care - Damai</option>
                        </select>
                    </div>

                    {{-- Penerima Manfaat (Hilang jika Pemasukan) --}}
                    <div class="space-y-2" id="penerimaContainer">
                        <label for="penerima_manfaat" class="text-sm font-semibold text-slate-700 ml-1">Jumlah Penerima
                            Manfaat (Jiwa)</label>
                        <input type="number" name="penerima_manfaat" id="penerima_manfaat" min="0"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm focus:border-green-500 focus:outline-none focus:ring-4 focus:ring-green-500/10"
                            placeholder="Contoh: 10">
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
                                oninput="calculateNet()"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm focus:border-green-500 focus:outline-none focus:ring-4 focus:ring-green-500/10">
                        </div>

                        {{-- Persentase (Hilang jika Pengeluaran) --}}
                        <div class="space-y-2" id="percentageContainer">
                            <label for="percentage" class="text-sm font-semibold text-slate-700 ml-1">Persentase
                                (%)</label>
                            <div class="relative">
                                <input type="number" name="percentage" id="percentage" value="10" readonly
                                    class="w-full rounded-2xl border border-slate-200 bg-zinc-100 p-3.5 pr-10 text-sm font-bold text-slate-500 cursor-not-allowed outline-none">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">%</span>
                            </div>
                        </div>
                    </div>

                    {{-- Jumlah Bersih (Hilang jika Pengeluaran) --}}
                    <div id="netContainer"
                        class="p-4 rounded-2xl bg-green-50 border border-green-100 flex items-center justify-between">
                        <div>
                            <div class="text-[10px] font-bold text-green-600 uppercase tracking-wider mb-0.5">Jumlah Bersih
                                (Total - 10%)</div>
                            <div class="text-2xl font-black text-green-900" id="netDisplay">Rp 0</div>
                        </div>
                        <i class="fas fa-calculator text-2xl text-green-200"></i>
                    </div>

                    <button type="submit"
                        class="w-full rounded-2xl bg-green-700 py-4 text-sm font-bold text-white shadow-lg shadow-green-200 transition-all hover:bg-green-800">
                        Simpan Transaksi
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Bulk Delete Form --}}
    <form id="bulkDeleteForm" action="{{ route('pc.infaq.bulk-delete') }}" method="POST" class="hidden">
        @csrf @method('DELETE')
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

                fp = flatpickr("#dateFilter", {
                    mode: "range",
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d M Y",
                    onChange: function() {
                        filterTable();
                    }
                });

                if (searchInput) searchInput.addEventListener('input', filterTable);

                if (selectAll) {
                    selectAll.addEventListener('change', function() {
                        rowCheckboxes.forEach(cb => {
                            if (!cb.closest('tr').classList.contains('hidden')) cb.checked = this
                                .checked;
                        });
                        updateBulkBtn();
                    });
                }
                rowCheckboxes.forEach(cb => cb.addEventListener('change', updateBulkBtn));
            });

            function toggleTransactionFields() {
                const type = document.getElementById('transaction_type').value;
                const infaqTypeSelect = document.getElementById('infaq_type');

                const containers = {
                    penerima: document.getElementById('penerimaContainer'),
                    pilar: document.getElementById('pilarContainer'),
                    percentage: document.getElementById('percentageContainer'),
                    net: document.getElementById('netContainer')
                };

                // Reset Options
                infaqTypeSelect.innerHTML = '';

                if (type === 'Pemasukan') {
                    // Logic 1: Sembunyikan penerima manfaat
                    containers.penerima.classList.add('hidden');
                    containers.penerima.querySelector('input').required = false;

                    // Logic 2: Dropdown Pemasukan
                    const options = ['Infaq UMKM', 'Infaq Toko', 'Infaq LP', 'Infaq Layanan', 'Infaq Lainnya'];
                    options.forEach(opt => {
                        let el = document.createElement('option');
                        el.value = opt;
                        el.text = opt;
                        infaqTypeSelect.appendChild(el);
                    });

                    // Logic 4: Tampilkan persentase & bersih
                    containers.percentage.classList.remove('hidden');
                    containers.net.classList.remove('hidden');
                    containers.pilar.classList.add('hidden');
                } else {
                    // Logic 3: Dropdown Pengeluaran
                    const options = ['Saldo Koin NU', 'Infaq Lain'];
                    options.forEach(opt => {
                        let el = document.createElement('option');
                        el.value = opt;
                        el.text = opt;
                        infaqTypeSelect.appendChild(el);
                    });

                    // Logic 4: Sembunyikan presentase & bersih
                    containers.percentage.classList.add('hidden');
                    containers.net.classList.add('hidden');

                    // Logic 5: Tampilkan Pilar & Penerima
                    containers.pilar.classList.remove('hidden');
                    containers.penerima.classList.remove('hidden');
                    containers.penerima.querySelector('input').required = true;
                }
            }

            function openCreateModal() {
                document.getElementById('modalTitle').textContent = 'Transmit Baru';
                document.getElementById('infaqForm').action = "{{ route('pc.infaq.index') }}";
                document.getElementById('methodField').innerHTML = '';
                document.getElementById('infaqForm').reset();
                document.getElementById('transaction_date').value = new Date().toISOString().split('T')[0];

                toggleTransactionFields();
                calculateNet();

                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalContainer.classList.remove('scale-95', 'opacity-0');
                }, 10);
            }

            function openEditModal(item) {
                document.getElementById('modalTitle').textContent = 'Edit Transaksi';
                document.getElementById('infaqForm').action = "/pc/infaq/" + item.id;
                document.getElementById('methodField').innerHTML = '@method('PUT')';

                document.getElementById('transaction_date').value = item.transaction_date;
                document.getElementById('transaction_type').value = item.transaction_type;

                toggleTransactionFields(); // Trigger dropdown contents

                document.getElementById('infaq_type').value = item.infaq_type;
                document.getElementById('penerima_manfaat').value = item.penerima_manfaat;
                document.getElementById('description').value = item.description;
                document.getElementById('gross_amount').value = item.gross_amount;

                if (item.transaction_type === 'Pengeluaran' && item.pilar_type) {
                    document.getElementById('pilar_type').value = item.pilar_type;
                }

                calculateNet();

                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalContainer.classList.remove('scale-95', 'opacity-0');
                }, 10);
            }

            function calculateNet() {
                const gross = parseFloat(document.getElementById('gross_amount').value) || 0;
                const net = Math.floor(gross * 0.9);
                document.getElementById('netDisplay').textContent = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    maximumFractionDigits: 0
                }).format(net);
            }

            function closeModal() {
                modalContainer.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }

            function filterTable() {
                const query = searchInput.value.toLowerCase();
                const selectedDates = fp.selectedDates;

                tableRows.forEach(row => {
                    const rowDate = new Date(row.getAttribute('data-date'));
                    rowDate.setHours(0, 0, 0, 0);
                    const matchesSearch = row.getAttribute('data-search').includes(query);
                    let matchesDate = true;

                    if (selectedDates.length === 2) {
                        matchesDate = rowDate >= selectedDates[0] && rowDate <= selectedDates[1];
                    }
                    row.classList.toggle('hidden', !(matchesSearch && matchesDate));
                });
            }

            function updateBulkBtn() {
                const checkedCount = Array.from(rowCheckboxes).filter(cb => cb.checked).length;
                bulkDeleteBtn.disabled = checkedCount === 0;
                selectedCountDisp.textContent = checkedCount;
            }

            function confirmDelete(formId) {
                Swal.fire({
                    title: 'Hapus Data?',
                    text: "Data akan dihapus permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) document.getElementById(formId).submit();
                });
            }
        </script>
    @endpush
@endsection
