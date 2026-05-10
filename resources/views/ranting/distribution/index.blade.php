@extends('layouts.app')

@section('page_title', 'Catat Pentasarufan')
@section('page_subtitle', 'Input data pentasarufan/pendistribusian dana.')

@section('content')
    <div class="w-full">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/60">
                <h2 class="text-xl font-semibold text-slate-800">
                    Form Input Pentasarufan
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Isi detail kegiatan pentasarufan di bawah ini untuk disetujui oleh MWC NU di Wilayah Masing-Masing
                </p>
            </div>

            <div class="p-6">
                <form action="{{ route('ranting.distribution.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="date" class="text-sm font-medium text-slate-700">
                                Tanggal Kegiatan
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
                            <label for="jenis_pilar" class="text-sm font-medium text-slate-700">
                                Jenis Pilar
                            </label>
                            <select
                                name="jenis_pilar"
                                id="jenis_pilar"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                required
                            >
                                <option value="" disabled selected>Pilih Jenis Pilar</option>
                                <option value="NU Care - Cerdas" {{ old('jenis_pilar') == 'NU Care - Cerdas' ? 'selected' : '' }}>NU Care - Cerdas</option>
                                <option value="NU Care - Sehat" {{ old('jenis_pilar') == 'NU Care - Sehat' ? 'selected' : '' }}>NU Care - Sehat</option>
                                <option value="NU Care - Berdaya" {{ old('jenis_pilar') == 'NU Care - Berdaya' ? 'selected' : '' }}>NU Care - Berdaya</option>
                                <option value="NU Care - Hijau" {{ old('jenis_pilar') == 'NU Care - Hijau' ? 'selected' : '' }}>NU Care - Hijau</option>
                                <option value="NU Care - Damai" {{ old('jenis_pilar') == 'NU Care - Damai' ? 'selected' : '' }}>NU Care - Damai</option>
                            </select>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label for="deskripsi" class="text-sm font-medium text-slate-700">
                                Deskripsi Kegiatan / Penerima
                            </label>
                            <input
                                type="text"
                                name="deskripsi"
                                id="deskripsi"
                                value="{{ old('deskripsi') }}"
                                placeholder="Contoh: Santunan Anak Yatim Desa A"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                required
                            >
                        </div>

                        <div class="space-y-2">
                            <label for="jumlah_pentasarufan_ranting" class="text-sm font-medium text-slate-700">
                                Jumlah Pentasarufan (Rp)
                            </label>
                            <input
                                type="number"
                                name="jumlah_pentasarufan_ranting"
                                id="jumlah_pentasarufan_ranting"
                                min="0"
                                step="1"
                                value="{{ old('jumlah_pentasarufan_ranting') }}"
                                placeholder="Masukkan jumlah nominal"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                required
                            >
                        </div>

                        <div class="space-y-2">
                            <label for="jumlah_penerima_manfaat_ranting" class="text-sm font-medium text-slate-700">
                                Jumlah Penerima Manfaat
                            </label>
                            <input
                                type="number"
                                name="jumlah_penerima_manfaat_ranting"
                                id="jumlah_penerima_manfaat_ranting"
                                min="0"
                                step="1"
                                value="{{ old('jumlah_penerima_manfaat_ranting', 0) }}"
                                placeholder="Contoh: 10"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                required
                            >
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label for="file_dokumentasi" class="text-sm font-medium text-slate-700">
                                Dokumentasi (Foto/PDF)
                            </label>
                            <input
                                type="file"
                                name="file_dokumentasi"
                                id="file_dokumentasi"
                                accept="image/*"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-800 file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200"
                            >
                            <p class="text-xs text-slate-500 italic">*JPG, JPEG, PNG, WEBP. Max 2MB.</p>
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
                            class="inline-flex items-center rounded-xl bg-green-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-green-800 transition"
                        >
                            <i class="fas fa-save mr-2"></i> Simpan Data
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
                        <h2 class="text-xl font-semibold text-slate-800">Riwayat Pentasarufan</h2>
                        <p class="text-sm text-slate-500 mt-1">Daftar kegiatan pentasarufan yang telah dicatat.</p>
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

            <form id="bulkDeleteForm" action="{{ route('ranting.distribution.bulk-delete') }}" method="POST">
                @csrf
                @method('DELETE')

                <div class="max-h-[500px] overflow-y-auto overflow-x-auto">
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
                                <th class="px-6 py-4 font-semibold min-w-[120px] text-center">Kode Transaksi</th>
                                <th class="px-6 py-4 font-semibold min-w-[100px] text-center">Tanggal</th>
                                <th class="px-6 py-4 font-semibold min-w-[200px] text-center">Deskripsi</th>
                                <th class="px-6 py-4 font-semibold min-w-[140px] text-center">Jumlah Pentasarufan</th>
                                <th class="px-6 py-4 font-semibold min-w-[100px] text-center">Jumlah Penerima Manfaat</th>
                                <th class="px-6 py-4 font-semibold min-w-[100px] text-center">Dokumentasi</th>
                                <th class="px-6 py-4 font-semibold min-w-[100px] text-center">Status</th>
                                <th class="px-6 py-4 font-semibold min-w-[100px] text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($items as $dist)
                                @php
                                    $isValidated = $dist->status === 'validated';
                                @endphp
                                <tr class="text-sm text-slate-700 hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4">
                                        @if (!$isValidated)
                                            <input
                                                type="checkbox"
                                                name="ids[]"
                                                value="{{ $dist->id }}"
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

                                    <td class="px-6 py-4 font-medium">{{ $dist->distribution_code }}</td>
                                    <td class="px-6 py-4 text-xs">{{ \Carbon\Carbon::parse($dist->date)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 max-w-[200px] truncate" title="{{ $dist->deskripsi }}">
                                        <div class="font-medium text-slate-900">{{ $dist->deskripsi }}</div>
                                        <div class="text-[10px] text-slate-500">{{ $dist->jenis_pilar }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold">Rp {{ number_format($dist->jumlah_pentasarufan_ranting, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                            {{ $dist->jumlah_penerima_manfaat_ranting ?? 0 }} Org
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($dist->file_dokumentasi)
                                            <div class="group relative inline-block">
                                                <img 
                                                    src="{{ asset($dist->file_dokumentasi) }}" 
                                                    alt="Dokumentasi" 
                                                    class="h-10 w-10 cursor-pointer rounded-lg object-cover ring-1 ring-slate-200 transition hover:ring-green-500"
                                                    onclick="viewPhoto('{{ asset($dist->file_dokumentasi) }}', '{{ $dist->deskripsi }}')"
                                                >
                                                <div class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 rounded bg-slate-800 px-2 py-1 text-[10px] text-white opacity-0 transition group-hover:opacity-100">
                                                    Klik untuk lihat
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-slate-400 text-xs italic">No Photo</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($dist->status === 'validated')
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-1 text-[10px] font-medium text-green-700 uppercase">
                                                validated
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-medium text-amber-700 uppercase">
                                                {{ $dist->status }}
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
                                                    data-id="{{ $dist->id }}"
                                                    data-date="{{ \Carbon\Carbon::parse($dist->date)->format('Y-m-d') }}"
                                                    data-jenis_pilar="{{ $dist->jenis_pilar }}"
                                                    data-deskripsi="{{ $dist->deskripsi }}"
                                                    data-jumlah_pentasarufan_ranting="{{ $dist->jumlah_pentasarufan_ranting }}"
                                                    data-jumlah_penerima_manfaat_ranting="{{ $dist->jumlah_penerima_manfaat_ranting }}"
                                                    data-status="{{ $dist->status }}"
                                                    data-update_url="{{ route('ranting.distribution.update', $dist->id) }}"
                                                >
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <form id="delete-form-{{ $dist->id }}" action="{{ route('ranting.distribution.destroy', $dist->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="button"
                                                        onclick="confirmDelete('delete-form-{{ $dist->id }}')"
                                                        class="p-2 text-slate-400 hover:text-red-600 transition"
                                                        title="Hapus"
                                                    >
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-[10px] text-slate-400 font-medium bg-slate-100 px-2 py-1 rounded-full border border-slate-200">TERKUNCI</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-slate-500 italic">
                                        Belum ada data pentasarufan.
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
            <div class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/60 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800">Edit Data Pentasarufan</h3>
                        <p class="text-sm text-slate-500 mt-1">Perbarui detail kegiatan pentasarufan.</p>
                    </div>
                    <button type="button" id="closeEditModal" class="text-slate-400 hover:text-slate-700 text-xl leading-none">
                        &times;
                    </button>
                </div>

                <div class="p-6">
                    <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="edit_date" class="text-sm font-medium text-slate-700">Tanggal Kegiatan</label>
                                <input
                                    type="date"
                                    name="date"
                                    id="edit_date"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                    required
                                >
                            </div>

                            <div class="space-y-2">
                                <label for="edit_jenis_pilar" class="text-sm font-medium text-slate-700">Jenis Pilar</label>
                                <select
                                    name="jenis_pilar"
                                    id="edit_jenis_pilar"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                    required
                                >
                                    <option value="" disabled selected>Pilih Jenis Pilar</option>
                                    <option value="NU Care - Cerdas">NU Care - Cerdas</option>
                                    <option value="NU Care - Sehat">NU Care - Sehat</option>
                                    <option value="NU Care - Berdaya">NU Care - Berdaya</option>
                                    <option value="NU Care - Hijau">NU Care - Hijau</option>
                                    <option value="NU Care - Damai">NU Care - Damai</option>
                                </select>
                            </div>

                            <div class="space-y-2 md:col-span-2"> 
                                <label for="edit_deskripsi" class="text-sm font-medium text-slate-700">Deskripsi Kegiatan / Penerima</label>
                                <input
                                    type="text"
                                    name="deskripsi"
                                    id="edit_deskripsi"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                    required
                                >
                            </div>
                            <div class="space-y-2">
                                <label for="edit_jumlah_pentasarufan_ranting" class="text-sm font-medium text-slate-700">Jumlah Pentasarufan (Rp)</label>
                                <input
                                    type="number"
                                    name="jumlah_pentasarufan_ranting"
                                    id="edit_jumlah_pentasarufan_ranting"
                                    min="0"
                                    step="1"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                    required
                                >
                            </div>

                            <div class="space-y-2">
                                <label for="edit_jumlah_penerima_manfaat_ranting" class="text-sm font-medium text-slate-700">Jumlah Penerima Manfaat</label>
                                <input
                                    type="number"
                                    name="jumlah_penerima_manfaat_ranting"
                                    id="edit_jumlah_penerima_manfaat_ranting"
                                    min="0"
                                    step="1"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                    required
                                >
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <label for="edit_file_dokumentasi" class="text-sm font-medium text-slate-700">Dokumentasi (Baru)</label>
                                <input
                                    type="file"
                                    name="file_dokumentasi"
                                    id="edit_file_dokumentasi"
                                    accept="image/*"
                                    class="w-full text-xs"
                                >
                                <p class="text-[10px] text-slate-400 italic">Format: JPG, JPEG, PNG, WEBP. Max 2MB. (Auto WebP Compression)</p>
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
                                class="inline-flex items-center rounded-xl bg-green-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-green-800 transition"
                            >
                                <i class="fas fa-check mr-2"></i> Update Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editModal = document.getElementById('editModal');
            const editModalOverlay = document.getElementById('editModalOverlay');
            const closeEditModal = document.getElementById('closeEditModal');
            const cancelEditModal = document.getElementById('cancelEditModal');
            const editForm = document.getElementById('editForm');

            const editDate = document.getElementById('edit_date');
            const editJenisPilar = document.getElementById('edit_jenis_pilar');
            const editDeskripsi = document.getElementById('edit_deskripsi');
            const editJumlahPentasarufanRanting = document.getElementById('edit_jumlah_pentasarufan_ranting');
            const editJumlahPenerimaManfaatRanting = document.getElementById('edit_jumlah_penerima_manfaat_ranting');

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
                    editJenisPilar.value = this.dataset.jenis_pilar;
                    editDeskripsi.value = this.dataset.deskripsi;
                    editJumlahPentasarufanRanting.value = this.dataset.jumlah_pentasarufan_ranting;
                    editJumlahPenerimaManfaatRanting.value = this.dataset.jumlah_penerima_manfaat_ranting;

                    openEditModal();
                });
            });

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
        function viewPhoto(url, title) {
            Swal.fire({
                title: title,
                imageUrl: url,
                imageAlt: title,
                showCloseButton: true,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-3xl border-none shadow-2xl',
                    title: 'text-zinc-800 font-semibold text-lg pt-4',
                    image: 'rounded-2xl max-h-[70vh] object-contain'
                }
            });
        }
    </script>
@endsection
