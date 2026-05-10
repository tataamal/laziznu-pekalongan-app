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

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label for="date" class="text-sm font-medium text-slate-700">
                                Tanggal/Bulan
                            </label>
                            <input type="date" name="date" id="date"
                                value="{{ old('date', now()->format('Y-m-d')) }}"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                required>
                        </div>

                        <div class="space-y-2">
                            <label for="jumlah_kaleng" class="text-sm font-medium text-slate-700">
                                Jumlah Kaleng
                            </label>
                            <input type="number" name="jumlah_kaleng" id="jumlah_kaleng" min="0" step="1"
                                value="{{ old('jumlah_kaleng', 0) }}" placeholder="Masukkan jumlah kaleng"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                required>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Status</label>
                            <div
                                class="flex items-center h-[42px] px-4 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-600">
                                pending
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label for="pemasukan_koin_nu_kotor" class="text-sm font-medium text-slate-700">
                                Perolehan Koin NU
                            </label>
                            <input type="number" name="pemasukan_koin_nu_kotor" id="pemasukan_koin_nu_kotor" min="0" step="1"
                                value="{{ old('pemasukan_koin_nu_kotor') }}" placeholder="Masukkan Perolehan KOIN NU"
                                class="calc-input w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                required>
                        </div>

                        <div class="space-y-2">
                            <label for="jasa_petugas" class="text-sm font-medium text-slate-700">
                                Jasa Petugas
                            </label>
                            <input type="number" name="jasa_petugas" id="jasa_petugas" min="0"
                                step="1" value="{{ old('jasa_petugas') }}" placeholder="Masukkan jasa petugas"
                                class="calc-input w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                required>
                        </div>

                        <div class="space-y-2">
                            <label for="pemasukan_koin_nu_bersih" class="text-sm font-medium text-slate-700">
                                Perolehan Bersih
                            </label>
                            <input type="number" name="pemasukan_koin_nu_bersih" id="pemasukan_koin_nu_bersih" value="0"
                                class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                readonly>
                            <p class="text-xs text-slate-500">Perolehan Koin NU - Jasa Petugas</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label for="koin_nu_ranting" class="text-sm font-medium text-slate-700">
                                Koin NU Ranting
                            </label>
                            <input type="number" name="koin_nu_ranting" id="koin_nu_ranting" value="0"
                                class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                readonly>
                            <p class="text-xs text-slate-500">Koin NU Ranting = 60% dari Perolehan Bersih</p>
                        </div>
                        <div class="space-y-2">
                            <label for="koin_nu_mwc" class="text-sm font-medium text-slate-700">
                                Koin NU MWC
                            </label>
                            <input type="number" name="koin_nu_mwc" id="koin_nu_mwc" value="0"
                                class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                readonly>
                            <p class="text-xs text-slate-500">Koin NU MWC = 35% dari Perolehan Bersih</p>
                        </div>
                        <div class="space-y-2">
                            <label for="koin_nu_pc" class="text-sm font-medium text-slate-700">
                                Koin NU PC
                            </label>
                            <input type="number" name="koin_nu_pc" id="koin_nu_pc" value="0"
                                class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                readonly>
                            <p class="text-xs text-slate-500">Koin NU PC = 5% dari Perolehan Bersih</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label for="hak_amil_ranting" class="text-sm font-medium text-slate-700">
                                Hak Amil Ranting
                            </label>
                            <input type="number" name="hak_amil_ranting" id="hak_amil_ranting" value="0"
                                class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                readonly>
                            <p class="text-xs text-slate-500">Otomatis: Dana Koin NU Ranting × 20%</p>
                        </div>
                        <div class="space-y-2">
                            <label for="hak_amil_mwc" class="text-sm font-medium text-slate-700">
                                Hak Amil MWC
                            </label>
                            <input type="number" name="hak_amil_mwc" id="hak_amil_mwc" value="0"
                                class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                readonly>
                            <p class="text-xs text-slate-500">Otomatis: Dana Koin NU MWC × 20%</p>
                        </div>
                        <div class="space-y-2">
                            <label for="hak_amil_pc" class="text-sm font-medium text-slate-700">
                                Hak Amil PC
                            </label>
                            <input type="number" name="hak_amil_pc" id="hak_amil_pc" value="0"
                                class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                readonly>
                            <p class="text-xs text-slate-500">Otomatis: Dana Koin NU PC × 20%</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label for="dana_dapat_digunakan_ranting" class="text-sm font-medium text-slate-700">
                                Dana Dapat Digunakan Ranting
                            </label>
                            <input type="number" name="dana_dapat_digunakan_ranting" id="dana_dapat_digunakan_ranting" value="0"
                                class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                readonly>
                            <p class="text-xs text-slate-500">Koin NU Ranting - Hak Amil Ranting</p>
                        </div>
                        <div class="space-y-2">
                            <label for="dana_dapat_digunakan_mwc" class="text-sm font-medium text-slate-700">
                                Dana Dapat Digunakan MWC
                            </label>
                            <input type="number" name="dana_dapat_digunakan_mwc" id="dana_dapat_digunakan_mwc" value="0"
                                class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                readonly>
                            <p class="text-xs text-slate-500">Koin NU MWC - Hak Amil MWC</p>
                        </div>
                        <div class="space-y-2">
                            <label for="dana_dapat_digunakan_pc" class="text-sm font-medium text-slate-700">
                                Dana Dapat Digunakan PC
                            </label>
                            <input type="number" name="dana_dapat_digunakan_pc" id="dana_dapat_digunakan_pc" value="0"
                                class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                readonly>
                            <p class="text-xs text-slate-500">Koin NU PC - Hak Amil PC</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="reset"
                            class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                            Reset
                        </button>

                        <button type="submit"
                            class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800 transition">
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
                        <button type="button" id="deleteSelectedBtn"
                            class="inline-flex items-center rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
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
                                    <input type="checkbox" id="selectAll"
                                        class="rounded border-slate-300 text-slate-900 focus:ring-slate-300">
                                </th>
                                <th class="px-6 py-4 font-semibold text-center">Kode Transaksi</th>
                                <th class="px-6 py-4 font-semibold text-center">Tanggal</th>
                                <th class="px-6 py-4 font-semibold text-center">Jumlah Kaleng</th>
                                <th class="px-6 py-4 font-semibold text-center">Pemasukan Koin NU</th>
                                <th class="px-6 py-4 font-semibold text-center">Jasa Petugas</th>
                                <th class="px-6 py-4 font-semibold text-center">Pemasukan Koin NU Bersih</th>
                                <th class="px-6 py-4 font-semibold text-center">Koin NU Ranting</th>
                                <th class="px-6 py-4 font-semibold text-center">Hak Amil Ranting</th>
                                <th class="px-6 py-4 font-semibold text-center">Dana Dapat Digunakan Ranting</th>
                                <th class="px-6 py-4 font-semibold text-center">Status</th>
                                <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($items as $income)
                                @php
                                    $isValidated = $income->status === 'approved' || $income->status === 'rejected';
                                @endphp
                                <tr class="text-sm text-slate-700 hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4">
                                        @if (!$isValidated)
                                            <input type="checkbox" name="ids[]" value="{{ $income->id }}"
                                                class="row-checkbox rounded border-slate-300 text-slate-900 focus:ring-slate-300">
                                        @else
                                            <input type="checkbox" disabled
                                                class="rounded border-slate-200 bg-slate-100 cursor-not-allowed">
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 font-medium">{{ $income->transaction_code }}</td>
                                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($income->date)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4">{{ $income->jumlah_kaleng ?? 0 }}</td>
                                    <td class="px-6 py-4">Rp {{ number_format($income->pemasukan_koin_nu_kotor, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">Rp {{ number_format($income->jasa_petugas, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">Rp {{ number_format($income->pemasukan_koin_nu_bersih, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">Rp {{ number_format($income->koin_nu_ranting, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">Rp {{ number_format($income->hak_amil_ranting, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">Rp {{ number_format($income->dana_dapat_digunakan_ranting, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">
                                        @if ($income->status === 'approved')
                                            <span
                                                class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                                                approved
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">
                                                {{ $income->status }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if (!$isValidated)
                                                <button type="button"
                                                    class="edit-btn p-2 text-slate-400 hover:text-blue-600 transition"
                                                    title="Edit" data-id="{{ $income->id }}"
                                                    data-date="{{ \Carbon\Carbon::parse($income->date)->format('Y-m-d') }}"
                                                    data-jumlah_kaleng="{{ $income->jumlah_kaleng }}"
                                                    data-pemasukan_koin_nu_kotor="{{ $income->pemasukan_koin_nu_kotor }}"
                                                    data-jasa_petugas="{{ $income->jasa_petugas }}"
                                                    data-pemasukan_koin_nu_bersih="{{ $income->pemasukan_koin_nu_bersih }}"
                                                    data-koin_nu_ranting="{{ $income->koin_nu_ranting }}"
                                                    data-koin_nu_mwc="{{ $income->koin_nu_mwc }}"
                                                    data-koin_nu_pc="{{ $income->koin_nu_pc }}"
                                                    data-hak_amil_ranting="{{ $income->hak_amil_ranting }}"
                                                    data-hak_amil_mwc="{{ $income->hak_amil_mwc }}"
                                                    data-hak_amil_pc="{{ $income->hak_amil_pc }}"
                                                    data-dana_dapat_digunakan_ranting="{{ $income->dana_dapat_digunakan_ranting }}"
                                                    data-dana_dapat_digunakan_mwc="{{ $income->dana_dapat_digunakan_mwc }}"
                                                    data-dana_dapat_digunakan_pc="{{ $income->dana_dapat_digunakan_pc }}"
                                                    data-status="{{ $income->status }}"
                                                    data-update_url="{{ route('ranting.income.update', $income->id) }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <form id="delete-form-{{ $income->id }}"
                                                    action="{{ route('ranting.income.destroy', $income->id) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        onclick="confirmDelete('delete-form-{{ $income->id }}')"
                                                        class="p-2 text-slate-400 hover:text-red-600 transition"
                                                        title="Hapus">
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
                                    <td colspan="11" class="px-6 py-8 text-center text-slate-500 italic">
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
                    <button type="button" id="closeEditModal"
                        class="text-slate-400 hover:text-slate-700 text-xl leading-none">
                        &times;
                    </button>
                </div>

                <div class="p-6">
                    <form id="editForm" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label for="edit_date" class="text-sm font-medium text-slate-700">Tanggal/Bulan</label>
                                <input type="date" name="date" id="edit_date"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                    required>
                            </div>

                            <div class="space-y-2">
                                <label for="edit_jumlah_kaleng" class="text-sm font-medium text-slate-700">Jumlah Kaleng</label>
                                <input type="number" name="jumlah_kaleng" id="edit_jumlah_kaleng" min="0" step="1"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                    required>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-slate-700">Status</label>
                                <div id="edit_status"
                                    class="flex items-center h-[42px] px-4 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-600">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label for="edit_pemasukan_koin_nu_kotor" class="text-sm font-medium text-slate-700">Perolehan Koin NU</label>
                                <input type="number" name="pemasukan_koin_nu_kotor" id="edit_pemasukan_koin_nu_kotor" min="0" step="1"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                    required>
                            </div>

                            <div class="space-y-2">
                                <label for="edit_jasa_petugas" class="text-sm font-medium text-slate-700">Jasa Petugas</label>
                                <input type="number" name="jasa_petugas" id="edit_jasa_petugas" min="0" step="1"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                    required>
                            </div>

                            <div class="space-y-2">
                                <label for="edit_pemasukan_koin_nu_bersih" class="text-sm font-medium text-slate-700">Perolehan Bersih</label>
                                <input type="number" id="edit_pemasukan_koin_nu_bersih"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                    readonly>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label for="edit_koin_nu_ranting" class="text-sm font-medium text-slate-700">Koin NU Ranting</label>
                                <input type="number" id="edit_koin_nu_ranting"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                    readonly>
                            </div>
                            <div class="space-y-2">
                                <label for="edit_koin_nu_mwc" class="text-sm font-medium text-slate-700">Koin NU MWC</label>
                                <input type="number" id="edit_koin_nu_mwc"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                    readonly>
                            </div>
                            <div class="space-y-2">
                                <label for="edit_koin_nu_pc" class="text-sm font-medium text-slate-700">Koin NU PC</label>
                                <input type="number" id="edit_koin_nu_pc"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                    readonly>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label for="edit_hak_amil_ranting" class="text-sm font-medium text-slate-700">Hak Amil Ranting</label>
                                <input type="number" id="edit_hak_amil_ranting"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                    readonly>
                            </div>
                            <div class="space-y-2">
                                <label for="edit_hak_amil_mwc" class="text-sm font-medium text-slate-700">Hak Amil MWC</label>
                                <input type="number" id="edit_hak_amil_mwc"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                    readonly>
                            </div>
                            <div class="space-y-2">
                                <label for="edit_hak_amil_pc" class="text-sm font-medium text-slate-700">Hak Amil PC</label>
                                <input type="number" id="edit_hak_amil_pc"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                    readonly>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label for="edit_dana_dapat_digunakan_ranting" class="text-sm font-medium text-slate-700">Dana Dapat Digunakan Ranting</label>
                                <input type="number" id="edit_dana_dapat_digunakan_ranting"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                    readonly>
                            </div>
                            <div class="space-y-2">
                                <label for="edit_dana_dapat_digunakan_mwc" class="text-sm font-medium text-slate-700">Dana Dapat Digunakan MWC</label>
                                <input type="number" id="edit_dana_dapat_digunakan_mwc"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                    readonly>
                            </div>
                            <div class="space-y-2">
                                <label for="edit_dana_dapat_digunakan_pc" class="text-sm font-medium text-slate-700">Dana Dapat Digunakan PC</label>
                                <input type="number" id="edit_dana_dapat_digunakan_pc"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-700"
                                    readonly>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" id="cancelEditModal"
                                class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                                Batal
                            </button>

                            <button type="submit"
                                class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800 transition">
                                Update Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const grossProfit = document.getElementById('pemasukan_koin_nu_kotor');
            const operatingExpenses = document.getElementById('jasa_petugas');
            const netIncome = document.getElementById('pemasukan_koin_nu_bersih');
            const koinNuRanting = document.getElementById('koin_nu_ranting');
            const koinNuMwc = document.getElementById('koin_nu_mwc');
            const koinNuPc = document.getElementById('koin_nu_pc');
            const hakAmilRanting = document.getElementById('hak_amil_ranting');
            const hakAmilMwc = document.getElementById('hak_amil_mwc');
            const hakAmilPc = document.getElementById('hak_amil_pc');
            const danaRanting = document.getElementById('dana_dapat_digunakan_ranting');
            const danaMwc = document.getElementById('dana_dapat_digunakan_mwc');
            const danaPc = document.getElementById('dana_dapat_digunakan_pc');

            function calculateCreateForm() {
                const gross = parseInt(grossProfit.value || 0);
                const expense = parseInt(operatingExpenses.value || 0);

                const net = Math.max(gross - expense, 0);
                const ranting = Math.round(net * 0.60);
                const mwc = Math.round(net * 0.35);
                const pc = Math.round(net * 0.05);
                
                const amilRanting = Math.round(ranting * 0.20);
                const amilMwc = Math.round(mwc * 0.20);
                const amilPc = Math.round(pc * 0.20);
                
                const danaR = ranting - amilRanting;
                const danaM = mwc - amilMwc;
                const danaP = pc - amilPc;

                netIncome.value = net;
                koinNuRanting.value = ranting;
                koinNuMwc.value = mwc;
                koinNuPc.value = pc;
                hakAmilRanting.value = amilRanting;
                hakAmilMwc.value = amilMwc;
                hakAmilPc.value = amilPc;
                danaRanting.value = danaR;
                danaMwc.value = danaM;
                danaPc.value = danaP;
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
            const editJumlahKaleng = document.getElementById('edit_jumlah_kaleng');
            const editGrossProfit = document.getElementById('edit_pemasukan_koin_nu_kotor');
            const editOperatingExpenses = document.getElementById('edit_jasa_petugas');
            const editNetIncome = document.getElementById('edit_pemasukan_koin_nu_bersih');
            const editKoinNuRanting = document.getElementById('edit_koin_nu_ranting');
            const editKoinNuMwc = document.getElementById('edit_koin_nu_mwc');
            const editKoinNuPc = document.getElementById('edit_koin_nu_pc');
            const editHakAmilRanting = document.getElementById('edit_hak_amil_ranting');
            const editHakAmilMwc = document.getElementById('edit_hak_amil_mwc');
            const editHakAmilPc = document.getElementById('edit_hak_amil_pc');
            const editDanaRanting = document.getElementById('edit_dana_dapat_digunakan_ranting');
            const editDanaMwc = document.getElementById('edit_dana_dapat_digunakan_mwc');
            const editDanaPc = document.getElementById('edit_dana_dapat_digunakan_pc');
            const editStatus = document.getElementById('edit_status');

            function calculateEditForm() {
                const gross = parseInt(editGrossProfit.value || 0);
                const expense = parseInt(editOperatingExpenses.value || 0);

                const net = Math.max(gross - expense, 0);
                const ranting = Math.round(net * 0.60);
                const mwc = Math.round(net * 0.35);
                const pc = Math.round(net * 0.05);
                
                const amilRanting = Math.round(ranting * 0.20);
                const amilMwc = Math.round(mwc * 0.20);
                const amilPc = Math.round(pc * 0.20);
                
                const danaR = ranting - amilRanting;
                const danaM = mwc - amilMwc;
                const danaP = pc - amilPc;

                editNetIncome.value = net;
                editKoinNuRanting.value = ranting;
                editKoinNuMwc.value = mwc;
                editKoinNuPc.value = pc;
                editHakAmilRanting.value = amilRanting;
                editHakAmilMwc.value = amilMwc;
                editHakAmilPc.value = amilPc;
                editDanaRanting.value = danaR;
                editDanaMwc.value = danaM;
                editDanaPc.value = danaP;
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
                button.addEventListener('click', function() {
                    editForm.action = this.dataset.update_url;
                    editDate.value = this.dataset.date;
                    editJumlahKaleng.value = this.dataset.jumlah_kaleng || 0;
                    editGrossProfit.value = this.dataset.pemasukan_koin_nu_kotor;
                    editOperatingExpenses.value = this.dataset.jasa_petugas;
                    editNetIncome.value = this.dataset.pemasukan_koin_nu_bersih;
                    editKoinNuRanting.value = this.dataset.koin_nu_ranting;
                    editKoinNuMwc.value = this.dataset.koin_nu_mwc;
                    editKoinNuPc.value = this.dataset.koin_nu_pc;
                    editHakAmilRanting.value = this.dataset.hak_amil_ranting;
                    editHakAmilMwc.value = this.dataset.hak_amil_mwc;
                    editHakAmilPc.value = this.dataset.hak_amil_pc;
                    editDanaRanting.value = this.dataset.dana_dapat_digunakan_ranting;
                    editDanaMwc.value = this.dataset.dana_dapat_digunakan_mwc;
                    editDanaPc.value = this.dataset.dana_dapat_digunakan_pc;
                    editStatus.textContent = this.dataset.status;

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

            document.addEventListener('keydown', function(e) {
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
                selectAll.addEventListener('change', function() {
                    rowCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateDeleteSelectedState();
                });
            }

            rowCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const total = rowCheckboxes.length;
                    const checked = document.querySelectorAll('.row-checkbox:checked').length;

                    if (selectAll) {
                        selectAll.checked = total > 0 && total === checked;
                    }

                    updateDeleteSelectedState();
                });
            });

            deleteSelectedBtn.addEventListener('click', function() {
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
