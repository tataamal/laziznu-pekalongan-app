@extends('layouts.app')

@section('page_title', 'Catat Pemasukan dari Koin NU')
@section('page_subtitle', 'Input data pemasukan Koin NU.')

@section('content')
    @php
        $isEdit = isset($item);
    @endphp

    <div class="w-full">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/60">
                <h2 class="text-xl font-semibold text-slate-800">
                    Form Input Pemasukan Koin NU
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Isi data utama, sisanya akan dihitung otomatis oleh sistem.
                </p>
            </div>

            <div class="p-6">
                @if (session('success'))
                    <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('ranting.income.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="date" class="text-sm font-medium text-slate-700">
                                Tanggal/Bulan
                            </label>
                            <input
                                type="date"
                                name="date"
                                id="date"
                                value="{{ old('date', now()->format('Y-m-d')) }}"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                required
                            >
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Status</label>
                            <div class="flex items-center h-[42px] px-4 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-600">
                                on_process
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="gross_profit" class="text-sm font-medium text-slate-700">
                                Perolehan Total (Rp)
                            </label>
                            <input
                                type="number"
                                name="gross_profit"
                                id="gross_profit"
                                min="0"
                                step="1"
                                value="{{ old('gross_profit') }}"
                                placeholder="Masukkan jumlah total"
                                class="calc-input w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                required
                            >
                        </div>

                        <div class="space-y-2">
                            <label for="operating_expenses" class="text-sm font-medium text-slate-700">
                                Jasa Petugas (Rp)
                            </label>
                            <input
                                type="number"
                                name="operating_expenses"
                                id="operating_expenses"
                                min="0"
                                step="1"
                                value="{{ old('operating_expenses') }}"
                                placeholder="Masukkan jasa petugas"
                                class="calc-input w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                required
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="net_income" class="text-sm font-medium text-slate-700">
                                Perolehan Bersih (Rp)
                            </label>
                            <input
                                type="number"
                                name="net_income"
                                id="net_income"
                                value="0"
                                class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                readonly
                            >
                            <p class="text-xs text-slate-500">Otomatis: Perolehan Total - Jasa Petugas</p>
                        </div>

                        <div class="space-y-2">
                            <label for="percentage" class="text-sm font-medium text-slate-700">
                                Presentase (%)
                            </label>
                            <input
                                type="number"
                                name="percentage"
                                id="percentage"
                                value="60"
                                class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                readonly
                            >
                            <p class="text-xs text-slate-500">Otomatis tetap 60% untuk ranting</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="allowed_budget" class="text-sm font-medium text-slate-700">
                                Dana dapat Digunakan (Rp)
                            </label>
                            <input
                                type="number"
                                name="allowed_budget"
                                id="allowed_budget"
                                value="0"
                                class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                readonly
                            >
                            <p class="text-xs text-slate-500">Otomatis: Perolehan Bersih × 60%</p>
                        </div>

                        <div class="space-y-2">
                            <label for="hak_amil" class="text-sm font-medium text-slate-700">
                                Dana Operasional Amil (Rp)
                            </label>
                            <input
                                type="number"
                                name="hak_amil"
                                id="hak_amil"
                                value="0"
                                class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                readonly
                            >
                            <p class="text-xs text-slate-500">Otomatis: Dana dapat Digunakan × 20%</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button
                            type="reset"
                            class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition"
                        >
                            Reset
                        </button>

                        <button
                            type="submit"
                            class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800 transition"
                        >
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="w-full mt-8 mb-8">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/60">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-800">Riwayat Pemasukan Koin NU</h2>
                        <p class="text-sm text-slate-500 mt-1">Daftar pemasukan yang baru saja diinputkan.</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            id="deleteSelectedBtn"
                            class="inline-flex items-center rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled
                        >
                            Hapus Selected
                        </button>
                    </div>
                </div>
            </div>

            <form id="bulkDeleteForm" action="{{ route('ranting.income.bulk-delete') }}" method="POST">
                @csrf
                @method('DELETE')

                <div class="max-h-[320px] overflow-y-auto overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead class="bg-slate-50 sticky top-0 z-10">
                            <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                                <th class="px-6 py-4 font-semibold w-[60px]">
                                    <input
                                        type="checkbox"
                                        id="selectAll"
                                        class="rounded border-slate-300 text-slate-900 focus:ring-slate-300"
                                    >
                                </th>
                                <th class="px-6 py-4 font-semibold">Kode</th>
                                <th class="px-6 py-4 font-semibold">Tanggal</th>
                                <th class="px-6 py-4 font-semibold">Perolehan Total</th>
                                <th class="px-6 py-4 font-semibold">Status</th>
                                <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($items as $income)
                                @php
                                    $isValidated = $income->status === 'validated';
                                @endphp
                                <tr class="text-sm text-slate-700 hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4">
                                        @if (!$isValidated)
                                            <input
                                                type="checkbox"
                                                name="ids[]"
                                                value="{{ $income->id }}"
                                                class="row-checkbox rounded border-slate-300 text-slate-900 focus:ring-slate-300"
                                            >
                                        @else
                                            <input
                                                type="checkbox"
                                                disabled
                                                class="rounded border-slate-200 bg-slate-100 cursor-not-allowed"
                                            >
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 font-medium">{{ $income->transaction_code }}</td>
                                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($income->date)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4">Rp {{ number_format($income->gross_profit, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">
                                        @if ($income->status === 'validated')
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                                                validated
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">
                                                {{ $income->status }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if (!$isValidated)
                                                <button
                                                    type="button"
                                                    class="edit-btn p-2 text-slate-400 hover:text-blue-600 transition"
                                                    title="Edit"
                                                    data-id="{{ $income->id }}"
                                                    data-date="{{ \Carbon\Carbon::parse($income->date)->format('Y-m-d') }}"
                                                    data-gross_profit="{{ $income->gross_profit }}"
                                                    data-operating_expenses="{{ $income->operating_expenses }}"
                                                    data-net_income="{{ $income->net_income }}"
                                                    data-percentage="{{ $income->percentage }}"
                                                    data-allowed_budget="{{ $income->allowed_budget }}"
                                                    data-hak_amil="{{ $income->hak_amil }}"
                                                    data-status="{{ $income->status }}"
                                                    data-update_url="{{ route('ranting.income.update', $income->id) }}"
                                                >
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <form id="delete-form-{{ $income->id }}" action="{{ route('ranting.income.destroy', $income->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="button"
                                                        onclick="confirmDelete('delete-form-{{ $income->id }}')"
                                                        class="p-2 text-slate-400 hover:text-red-600 transition"
                                                        title="Hapus"
                                                    >
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-slate-400 italic">Terkunci</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-500 italic">
                                        Belum ada data pemasukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div id="editModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-900/50" id="editModalOverlay"></div>

        <div class="relative flex min-h-full items-center justify-center p-4">
            <div class="w-full max-w-3xl rounded-2xl bg-white shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/60 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800">Edit Pemasukan Koin NU</h3>
                        <p class="text-sm text-slate-500 mt-1">Perbarui data pemasukan melalui modal.</p>
                    </div>
                    <button type="button" id="closeEditModal" class="text-slate-400 hover:text-slate-700 text-xl leading-none">
                        &times;
                    </button>
                </div>

                <div class="p-6">
                    <form id="editForm" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="edit_date" class="text-sm font-medium text-slate-700">Tanggal/Bulan</label>
                                <input
                                    type="date"
                                    name="date"
                                    id="edit_date"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                    required
                                >
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-slate-700">Status</label>
                                <div id="edit_status" class="flex items-center h-[42px] px-4 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-600"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="edit_gross_profit" class="text-sm font-medium text-slate-700">Perolehan Total (Rp)</label>
                                <input
                                    type="number"
                                    name="gross_profit"
                                    id="edit_gross_profit"
                                    min="0"
                                    step="1"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                    required
                                >
                            </div>

                            <div class="space-y-2">
                                <label for="edit_operating_expenses" class="text-sm font-medium text-slate-700">Jasa Petugas (Rp)</label>
                                <input
                                    type="number"
                                    name="operating_expenses"
                                    id="edit_operating_expenses"
                                    min="0"
                                    step="1"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                    required
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="edit_net_income" class="text-sm font-medium text-slate-700">Perolehan Bersih (Rp)</label>
                                <input
                                    type="number"
                                    id="edit_net_income"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                    readonly
                                >
                            </div>

                            <div class="space-y-2">
                                <label for="edit_percentage" class="text-sm font-medium text-slate-700">Presentase (%)</label>
                                <input
                                    type="number"
                                    id="edit_percentage"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                    readonly
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="edit_allowed_budget" class="text-sm font-medium text-slate-700">Dana dapat Digunakan (Rp)</label>
                                <input
                                    type="number"
                                    id="edit_allowed_budget"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                    readonly
                                >
                            </div>

                            <div class="space-y-2">
                                <label for="edit_hak_amil" class="text-sm font-medium text-slate-700">Dana Operasional Amil (Rp)</label>
                                <input
                                    type="number"
                                    id="edit_hak_amil"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                    readonly
                                >
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button
                                type="button"
                                id="cancelEditModal"
                                class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition"
                            >
                                Batal
                            </button>

                            <button
                                type="submit"
                                class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800 transition"
                            >
                                Update Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const grossProfit = document.getElementById('gross_profit');
            const operatingExpenses = document.getElementById('operating_expenses');
            const netIncome = document.getElementById('net_income');
            const percentage = document.getElementById('percentage');
            const allowedBudget = document.getElementById('allowed_budget');
            const hakAmil = document.getElementById('hak_amil');

            function calculateCreateForm() {
                const gross = parseInt(grossProfit.value || 0);
                const expense = parseInt(operatingExpenses.value || 0);

                const net = Math.max(gross - expense, 0);
                const percentValue = 60;
                const allowed = Math.round(net * (percentValue / 100));
                const amil = Math.round(allowed * 0.20);

                netIncome.value = net;
                percentage.value = percentValue;
                allowedBudget.value = allowed;
                hakAmil.value = amil;
            }

            if (grossProfit && operatingExpenses) {
                grossProfit.addEventListener('input', calculateCreateForm);
                operatingExpenses.addEventListener('input', calculateCreateForm);
                calculateCreateForm();
            }

            const editModal = document.getElementById('editModal');
            const editModalOverlay = document.getElementById('editModalOverlay');
            const closeEditModal = document.getElementById('closeEditModal');
            const cancelEditModal = document.getElementById('cancelEditModal');
            const editForm = document.getElementById('editForm');

            const editDate = document.getElementById('edit_date');
            const editGrossProfit = document.getElementById('edit_gross_profit');
            const editOperatingExpenses = document.getElementById('edit_operating_expenses');
            const editNetIncome = document.getElementById('edit_net_income');
            const editPercentage = document.getElementById('edit_percentage');
            const editAllowedBudget = document.getElementById('edit_allowed_budget');
            const editHakAmil = document.getElementById('edit_hak_amil');
            const editStatus = document.getElementById('edit_status');

            function calculateEditForm() {
                const gross = parseInt(editGrossProfit.value || 0);
                const expense = parseInt(editOperatingExpenses.value || 0);

                const net = Math.max(gross - expense, 0);
                const percentValue = 60;
                const allowed = Math.round(net * (percentValue / 100));
                const amil = Math.round(allowed * 0.20);

                editNetIncome.value = net;
                editPercentage.value = percentValue;
                editAllowedBudget.value = allowed;
                editHakAmil.value = amil;
            }

            function openEditModal() {
                editModal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function hideEditModal() {
                editModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function () {
                    editForm.action = this.dataset.update_url;
                    editDate.value = this.dataset.date;
                    editGrossProfit.value = this.dataset.gross_profit;
                    editOperatingExpenses.value = this.dataset.operating_expenses;
                    editNetIncome.value = this.dataset.net_income;
                    editPercentage.value = this.dataset.percentage;
                    editAllowedBudget.value = this.dataset.allowed_budget;
                    editHakAmil.value = this.dataset.hak_amil;
                    editStatus.textContent = this.dataset.status;

                    calculateEditForm();
                    openEditModal();
                });
            });

            if (editGrossProfit && editOperatingExpenses) {
                editGrossProfit.addEventListener('input', calculateEditForm);
                editOperatingExpenses.addEventListener('input', calculateEditForm);
            }

            closeEditModal.addEventListener('click', hideEditModal);
            cancelEditModal.addEventListener('click', hideEditModal);
            editModalOverlay.addEventListener('click', hideEditModal);

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !editModal.classList.contains('hidden')) {
                    hideEditModal();
                }
            });

            const selectAll = document.getElementById('selectAll');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
            const bulkDeleteForm = document.getElementById('bulkDeleteForm');

            function updateDeleteSelectedState() {
                const checkedRows = document.querySelectorAll('.row-checkbox:checked');
                deleteSelectedBtn.disabled = checkedRows.length === 0;
            }

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    rowCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateDeleteSelectedState();
                });
            }

            rowCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    const total = rowCheckboxes.length;
                    const checked = document.querySelectorAll('.row-checkbox:checked').length;

                    if (selectAll) {
                        selectAll.checked = total > 0 && total === checked;
                    }

                    updateDeleteSelectedState();
                });
            });

            deleteSelectedBtn.addEventListener('click', function () {
                const checkedRows = document.querySelectorAll('.row-checkbox:checked');

                if (checkedRows.length === 0) {
                    alert('Pilih data yang ingin dihapus.');
                    return;
                }

                if (confirm('Yakin ingin menghapus data yang dipilih?')) {
                    bulkDeleteForm.submit();
                }
            });
        });

        function confirmDelete(formId) {
            if (confirm('Yakin ingin menghapus data ini?')) {
                document.getElementById(formId).submit();
            }
        }
    </script>
@endsection