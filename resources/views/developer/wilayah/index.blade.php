@extends('layouts.app')

@section('page_title', 'Kelola Wilayah')
@section('page_subtitle', 'Kelola data wilayah/cabang dan informasi PIC.')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-1 items-center gap-2">
            <form action="{{ route('developer.wilayah.index') }}" method="GET" class="relative w-full max-w-sm">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari wilayah, PIC, telpon..." class="w-full rounded-2xl border border-zinc-200 bg-white py-2.5 pl-10 pr-4 text-sm outline-none transition placeholder:text-zinc-400 focus:border-green-500 focus:ring-1 focus:ring-green-500">
            </form>

            <div id="bulkActions" class="hidden flex items-center gap-2">
                <button onclick="document.getElementById('bulkDeleteModal').classList.remove('hidden')" class="rounded-2xl border border-red-200 bg-red-50 text-red-600 px-4 py-2.5 text-sm font-semibold transition hover:bg-red-100">
                    <i class="fas fa-trash-alt mr-1"></i> Hapus <span class="bg-red-200 px-1.5 py-0.5 rounded-full text-xs" id="bulkDeleteCount">0</span>
                </button>
            </div>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('importModal').classList.remove('hidden')" class="rounded-2xl border border-zinc-200 bg-white px-4 py-2.5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50">
                <i class="fas fa-file-excel mr-2 text-green-600"></i> Import Excel
            </button>
            <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="rounded-2xl bg-green-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800">
                <i class="fas fa-plus mr-2"></i> Tambah Wilayah
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-2xl bg-green-50 p-4 text-sm text-green-700 border border-green-200">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-2xl bg-red-50 p-4 text-sm text-red-700 border border-red-200">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-2xl bg-red-50 p-4 text-sm text-red-700 border border-red-200">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="overflow-x-auto overflow-y-auto max-h-[450px] rounded-3xl border border-zinc-200 bg-white shadow-sm">
        <table class="w-full border-collapse">
            <thead class="sticky top-0 z-10 bg-zinc-50 outline outline-1 outline-zinc-100">
                <tr class="text-left text-xs uppercase tracking-wide text-zinc-500">
                    <th class="px-5 py-4 w-10 text-center">
                        <input type="checkbox" id="selectAll" class="h-4 w-4 rounded border-zinc-300 text-green-600 focus:ring-green-500 cursor-pointer">
                    </th>
                    <th class="px-5 py-4 font-semibold">Nama Wilayah</th>
                    <th class="px-5 py-4 font-semibold">Alamat</th>
                    <th class="px-5 py-4 font-semibold">PIC</th>
                    <th class="px-5 py-4 font-semibold">Kontak PIC</th>
                    <th class="px-5 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse($wilayahs as $wilayah)
                    <tr class="text-sm text-zinc-700 transition hover:bg-zinc-50/50">
                        <td class="px-5 py-4 text-center">
                            <input type="checkbox" value="{{ $wilayah->id }}" class="wilayah-checkbox h-4 w-4 rounded border-zinc-300 text-green-600 focus:ring-green-500 cursor-pointer" onchange="toggleBulkActions()">
                        </td>
                        <td class="px-5 py-4 font-medium text-zinc-900">{{ $wilayah->nama_wilayah }}</td>
                        <td class="px-5 py-4">{{ $wilayah->alamat ?? '-' }}</td>
                        <td class="px-5 py-4">{{ $wilayah->pic ?? '-' }}</td>
                        <td class="px-5 py-4">{{ $wilayah->telp_pic ?? '-' }}</td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="openEditModal({{ $wilayah->toJson() }})" class="rounded-xl p-2 text-zinc-400 transition hover:bg-zinc-100 hover:text-blue-600">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="openDeleteModal({{ $wilayah->id }})" class="rounded-xl p-2 text-zinc-400 transition hover:bg-zinc-100 hover:text-red-600">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-sm text-zinc-500">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i class="fas fa-map-marker-alt text-4xl text-zinc-300"></i>
                                <span>Belum ada data wilayah ditemukan.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($wilayahs->hasPages())
            <div class="border-t border-zinc-100 p-4">
                {{ $wilayahs->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Create Wilayah -->
    <div id="createModal" class="fixed inset-0 z-50 flex hidden items-center justify-center bg-zinc-900/50 px-4 backdrop-blur-sm transition-opacity">
        <div class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-xl sm:p-8">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-zinc-900">Tambah Wilayah Baru</h3>
                <button onclick="document.getElementById('createModal').classList.add('hidden')" class="text-zinc-400 hover:text-zinc-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('developer.wilayah.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="mb-1 block text-sm font-medium text-zinc-700">Nama Wilayah</label>
                    <input type="text" name="nama_wilayah" required class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm outline-none focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-zinc-700">Alamat</label>
                    <textarea name="alamat" rows="2" class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm outline-none focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-zinc-700">Nama PIC</label>
                        <input type="text" name="pic" class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm outline-none focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-zinc-700">No. Telpon PIC</label>
                        <input type="text" name="telp_pic" class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm outline-none focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="rounded-2xl px-5 py-2.5 text-sm font-semibold text-zinc-600 hover:bg-zinc-100">Batal</button>
                    <button type="submit" class="rounded-2xl bg-green-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-800">Simpan Wilayah</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Wilayah -->
    <div id="editModal" class="fixed inset-0 z-50 flex hidden items-center justify-center bg-zinc-900/50 px-4 backdrop-blur-sm transition-opacity">
        <div class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-xl sm:p-8">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-zinc-900">Edit Wilayah</h3>
                <button onclick="document.getElementById('editModal').classList.add('hidden')" class="text-zinc-400 hover:text-zinc-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="mb-1 block text-sm font-medium text-zinc-700">Nama Wilayah</label>
                    <input type="text" name="nama_wilayah" id="edit_nama_wilayah" required class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm outline-none focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-zinc-700">Alamat</label>
                    <textarea name="alamat" id="edit_alamat" rows="2" class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm outline-none focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-zinc-700">Nama PIC</label>
                        <input type="text" name="pic" id="edit_pic" class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm outline-none focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-zinc-700">No. Telpon PIC</label>
                        <input type="text" name="telp_pic" id="edit_telp_pic" class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm outline-none focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="rounded-2xl px-5 py-2.5 text-sm font-semibold text-zinc-600 hover:bg-zinc-100">Batal</button>
                    <button type="submit" class="rounded-2xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Perbarui Wilayah</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Delete Wilayah -->
    <div id="deleteModal" class="fixed inset-0 z-50 flex hidden items-center justify-center bg-zinc-900/50 px-4 backdrop-blur-sm transition-opacity">
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-xl sm:p-8 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-600">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>
            <h3 class="mb-2 text-lg font-semibold text-zinc-900">Hapus Wilayah</h3>
            <p class="mb-6 text-sm text-zinc-500">Apakah Anda yakin ingin menghapus wilayah ini? Tindakan ini tidak dapat dibatalkan.</p>
            <form id="deleteForm" method="POST" class="flex justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')" class="rounded-2xl border border-zinc-200 bg-white px-5 py-2.5 text-sm font-semibold text-zinc-700 hover:bg-zinc-50">Batal</button>
                <button type="submit" class="rounded-2xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Ya, Hapus</button>
            </form>
        </div>
    </div>

    <!-- Modal Import Excel -->
    <div id="importModal" class="fixed inset-0 z-50 flex hidden items-center justify-center bg-zinc-900/50 px-4 backdrop-blur-sm transition-opacity">
        <div class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-xl sm:p-8">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-zinc-900">Import Data Wilayah</h3>
                <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-zinc-400 hover:text-zinc-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-6 rounded-2xl bg-blue-50 p-4 text-sm text-blue-700 border border-blue-200">
                <p class="font-medium mb-1"><i class="fas fa-info-circle mr-1"></i> Format Import</p>
                <p class="text-xs">Pastikan file Excel (.xlsx) Anda memiliki header kolom: <b>nama_wilayah, alamat, pic, telp_pic</b>. Nama Wilayah wajib diisi.</p>
                <a href="{{ route('developer.wilayah.template') }}" class="mt-3 inline-flex items-center gap-2 rounded-xl bg-white px-3 py-1.5 text-xs font-semibold text-blue-700 shadow-sm border border-blue-200 hover:bg-blue-100 transition">
                    <i class="fas fa-download"></i> Download Template File
                </a>
            </div>
            <form action="{{ route('developer.wilayah.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="mb-1 block text-sm font-medium text-zinc-700">Pilih File Excel (.xlsx, .csv)</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2 text-sm outline-none file:mr-4 file:rounded-xl file:border-0 file:bg-green-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-green-700 hover:file:bg-green-200 cursor-pointer">
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="rounded-2xl px-5 py-2.5 text-sm font-semibold text-zinc-600 hover:bg-zinc-100">Batal</button>
                    <button type="submit" class="rounded-2xl bg-green-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-800">Upload Data</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Bulk Delete -->
    <div id="bulkDeleteModal" class="fixed inset-0 z-50 flex hidden items-center justify-center bg-zinc-900/50 px-4 backdrop-blur-sm transition-opacity">
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-xl sm:p-8 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-600">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>
            <h3 class="mb-2 text-lg font-semibold text-zinc-900">Hapus Data Massal</h3>
            <p class="mb-6 text-sm text-zinc-500">Apakah Anda yakin ingin menghapus puluhan wilayah yang dipilih? Tindakan ini permanen.</p>
            <form id="bulkDeleteForm" action="{{ route('developer.wilayah.bulk-delete') }}" method="POST" class="flex justify-center gap-3">
                @csrf
                <div id="bulkDeleteInputs"></div>
                <button type="button" onclick="document.getElementById('bulkDeleteModal').classList.add('hidden')" class="rounded-2xl border border-zinc-200 bg-white px-5 py-2.5 text-sm font-semibold text-zinc-700 hover:bg-zinc-50">Batal</button>
                <button type="submit" class="rounded-2xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Ya, Hapus Semua</button>
            </form>
        </div>
    </div>

    <script>
        const selectAllCheckbox = document.getElementById('selectAll');
        const wilayahCheckboxes = document.querySelectorAll('.wilayah-checkbox');
        const bulkActions = document.getElementById('bulkActions');
        const bulkDeleteCount = document.getElementById('bulkDeleteCount');
        const bulkDeleteInputsContainer = document.getElementById('bulkDeleteInputs');

        function toggleBulkActions() {
            let checkedCount = 0;
            let inputsHtml = '';
            
            wilayahCheckboxes.forEach(cb => {
                if(cb.checked) {
                    checkedCount++;
                    inputsHtml += `<input type="hidden" name="wilayah_ids[]" value="${cb.value}">`;
                }
            });

            if (checkedCount > 0) {
                bulkActions.classList.remove('hidden');
                bulkDeleteCount.innerText = checkedCount;
                bulkDeleteInputsContainer.innerHTML = inputsHtml;
            } else {
                bulkActions.classList.add('hidden');
            }

            if (wilayahCheckboxes.length > 0) {
                selectAllCheckbox.checked = (checkedCount === wilayahCheckboxes.length);
            }
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                wilayahCheckboxes.forEach(cb => {
                    cb.checked = selectAllCheckbox.checked;
                });
                toggleBulkActions();
            });
        }

        function openEditModal(wilayah) {
            const form = document.getElementById('editForm');
            form.action = `/developer/wilayah/${wilayah.id}`;
            document.getElementById('edit_nama_wilayah').value = wilayah.nama_wilayah;
            document.getElementById('edit_alamat').value = wilayah.alamat || '';
            document.getElementById('edit_pic').value = wilayah.pic || '';
            document.getElementById('edit_telp_pic').value = wilayah.telp_pic || '';
            document.getElementById('editModal').classList.remove('hidden');
        }

        function openDeleteModal(id) {
            const form = document.getElementById('deleteForm');
            form.action = `/developer/wilayah/${id}`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }
    </script>
@endsection
