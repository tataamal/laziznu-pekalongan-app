@extends('layouts.app')

@section('page_title', 'Kelola Data Munfiq')
@section('page_subtitle', 'Manajemen data munfiq dan kode kaleng.')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-1 items-center gap-2">
            <form action="{{ route('developer.management-munfiq.index') }}" method="GET" class="relative w-full max-w-sm">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400"></i>
                <input type="text" name="search" value="{{ $search ?? request('search') }}" placeholder="Cari nama, kode kaleng, atau ranting..." class="w-full rounded-2xl border border-zinc-200 bg-white py-2.5 pl-10 pr-4 text-sm outline-none transition placeholder:text-zinc-400 focus:border-green-500 focus:ring-1 focus:ring-green-500">
            </form>

            <div id="bulkActions" class="hidden flex items-center gap-2">
                <button type="button" onclick="confirmBulkDelete()" class="rounded-2xl border border-red-200 bg-red-50 text-red-600 px-4 py-2.5 text-sm font-semibold transition hover:bg-red-100">
                    <i class="fas fa-trash-alt mr-1"></i> Hapus <span class="bg-red-200 px-1.5 py-0.5 rounded-full text-xs" id="bulkDeleteCount">0</span>
                </button>
            </div>
        </div>
        <div class="flex gap-2">
            <form id="import-form" action="{{ route('developer.management-munfiq.import') }}" method="POST" enctype="multipart/form-data" class="hidden">
                @csrf
                <input type="file" name="file" id="import-file" accept=".xlsx,.xls,.csv" onchange="document.getElementById('import-form').submit();">
            </form>

            <a href="{{ route('developer.management-munfiq.template') }}" class="rounded-2xl border border-zinc-200 bg-white px-4 py-2.5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50">
                <i class="fas fa-download mr-2 text-zinc-600"></i> <span class="hidden sm:inline">Template</span>
            </a>
            <button type="button" onclick="document.getElementById('import-file').click()" class="rounded-2xl border border-zinc-200 bg-white px-4 py-2.5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50">
                <i class="fas fa-file-excel mr-2 text-green-600"></i> <span class="hidden sm:inline">Import Excel</span>
            </button>

            <a href="{{ route('developer.management-munfiq.create') }}" class="rounded-2xl bg-green-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800">
                <i class="fas fa-plus mr-2"></i> <span class="hidden sm:inline">Tambah Munfiq</span>
            </a>
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
                    <th class="px-5 py-4 font-semibold">No</th>
                    <th class="px-5 py-4 font-semibold">Kode Kaleng</th>
                    <th class="px-5 py-4 font-semibold">Nama Munfiq</th>
                    <th class="px-5 py-4 font-semibold">Ranting</th>
                    <th class="px-5 py-4 font-semibold">JK</th>
                    <th class="px-5 py-4 font-semibold">Status</th>
                    <th class="px-5 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse($munfiqs as $index => $munfiq)
                    <tr class="text-sm text-zinc-700 transition hover:bg-zinc-50/50">
                        <td class="px-5 py-4 text-center">
                            <input type="checkbox" value="{{ $munfiq->id }}" class="row-checkbox h-4 w-4 rounded border-zinc-300 text-green-600 focus:ring-green-500 cursor-pointer" onchange="toggleBulkActions()">
                        </td>
                        <td class="px-5 py-4">{{ $munfiqs->firstItem() + $index }}</td>
                        <td class="px-5 py-4 font-mono font-medium text-zinc-900">{{ $munfiq->kode_kaleng }}</td>
                        <td class="px-5 py-4 font-medium text-zinc-900">{{ $munfiq->nama }}</td>
                        <td class="px-5 py-4">{{ $munfiq->data_ranting->nama ?? '-' }}</td>
                        <td class="px-5 py-4">{{ $munfiq->jenis_kelamin }}</td>
                        <td class="px-5 py-4">
                            @if($munfiq->status === 'Aktif')
                                <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">Aktif</span>
                            @else
                                <span class="rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-semibold text-zinc-600">Pasif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('developer.management-munfiq.edit', $munfiq->id) }}" class="rounded-xl p-2 text-zinc-400 transition hover:bg-zinc-100 hover:text-blue-600" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form id="delete-form-{{ $munfiq->id }}" action="{{ route('developer.management-munfiq.destroy', $munfiq->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete('delete-form-{{ $munfiq->id }}')" class="rounded-xl p-2 text-zinc-400 transition hover:bg-zinc-100 hover:text-red-600" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-8 text-center text-sm text-zinc-500">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i class="fas fa-users text-4xl text-zinc-300"></i>
                                <span>Belum ada data munfiq ditemukan.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($munfiqs->hasPages())
            <div class="border-t border-zinc-100 p-4">
                {{ $munfiqs->links() }}
            </div>
        @endif
    </div>

<form id="bulk-delete-form" method="POST" action="{{ route('developer.management-munfiq.bulk-delete') }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    const selectAllCheckbox = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.row-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    const bulkDeleteCount = document.getElementById('bulkDeleteCount');
    const bulkDeleteForm = document.getElementById('bulk-delete-form');

    function toggleBulkActions() {
        let checkedCount = 0;
        
        checkboxes.forEach(cb => {
            if(cb.checked) {
                checkedCount++;
            }
        });

        if (checkedCount > 0) {
            bulkActions.classList.remove('hidden');
            bulkDeleteCount.innerText = checkedCount;
        } else {
            bulkActions.classList.add('hidden');
        }

        if (checkboxes.length > 0) {
            selectAllCheckbox.checked = (checkedCount === checkboxes.length);
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = selectAllCheckbox.checked;
            });
            toggleBulkActions();
        });
    }

    window.confirmBulkDelete = function() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        if (checkedBoxes.length === 0) return;

        Swal.fire({
            title: 'Hapus ' + checkedBoxes.length + ' data terpilih?',
            text: "Data munfiq yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                bulkDeleteForm.innerHTML = '@csrf @method("DELETE")';
                checkedBoxes.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = cb.value;
                    bulkDeleteForm.appendChild(input);
                });
                bulkDeleteForm.submit();
            }
        });
    }

    window.confirmDelete = function(formId) {
        Swal.fire({
            title: 'Hapus data munfiq?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
</script>
@endsection
