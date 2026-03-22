@extends('layouts.app')

@section('page_title', 'Kelola Data Munfiq')
@section('page_subtitle', 'Manajemen data munfiq dan kode kaleng.')

@section('content')
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="relative w-full sm:w-96">
        <form method="GET" action="{{ route('developer.management-munfiq.index') }}">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, kode kaleng, atau ranting..." class="w-full rounded-2xl border border-zinc-200 bg-white px-4 py-2.5 text-sm outline-none ring-0 placeholder:text-zinc-400 focus:border-green-300">
            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-green-600">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <div class="flex items-center gap-3">
        <button type="button" id="bulk-delete-btn" class="hidden flex items-center gap-2 rounded-2xl bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-100" onclick="confirmBulkDelete()">
            <i class="fas fa-trash"></i>
            <span>Hapus Terpilih</span>
        </button>
        <a href="{{ route('developer.management-munfiq.create') }}" class="flex items-center gap-2 rounded-2xl bg-green-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800">
            <i class="fas fa-plus"></i>
            <span>Tambah Munfiq</span>
        </a>
    </div>
</div>

<div class="rounded-3xl border border-zinc-200 bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left text-sm text-zinc-600">
            <thead class="bg-zinc-50 border-b border-zinc-200 text-zinc-500 uppercase tracking-wider text-xs font-semibold">
                <tr>
                    <th class="px-6 py-4 w-10">
                        <input type="checkbox" id="select-all" class="rounded border-zinc-300 text-green-600 focus:ring-green-500">
                    </th>
                    <th class="px-6 py-4">No</th>
                    <th class="px-6 py-4">Kode Kaleng</th>
                    <th class="px-6 py-4">Nama Munfiq</th>
                    <th class="px-6 py-4">Ranting</th>
                    <th class="px-6 py-4">JK</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse($munfiqs as $index => $munfiq)
                    <tr class="hover:bg-zinc-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <input type="checkbox" value="{{ $munfiq->id }}" class="row-checkbox rounded border-zinc-300 text-green-600 focus:ring-green-500">
                        </td>
                        <td class="px-6 py-4">{{ $munfiqs->firstItem() + $index }}</td>
                        <td class="px-6 py-4 font-mono font-medium text-zinc-800">{{ $munfiq->kode_kaleng }}</td>
                        <td class="px-6 py-4 font-medium text-zinc-800">{{ $munfiq->nama }}</td>
                        <td class="px-6 py-4">{{ $munfiq->data_ranting->nama ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $munfiq->jenis_kelamin }}</td>
                        <td class="px-6 py-4">
                            @if($munfiq->status === 'Aktif')
                                <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">Aktif</span>
                            @else
                                <span class="rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-semibold text-zinc-600">Pasif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('developer.management-munfiq.edit', $munfiq->id) }}" class="flex h-8 w-8 items-center justify-center rounded-xl bg-amber-50 text-amber-600 transition hover:bg-amber-100" title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form id="delete-form-{{ $munfiq->id }}" action="{{ route('developer.management-munfiq.destroy', $munfiq->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete('delete-form-{{ $munfiq->id }}')" class="flex h-8 w-8 items-center justify-center rounded-xl bg-red-50 text-red-600 transition hover:bg-red-100" title="Hapus">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-zinc-500">
                            Tidak ada data munfiq ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($munfiqs->hasPages())
        <div class="border-t border-zinc-200 p-4">
            {{ $munfiqs->links() }}
        </div>
    @endif
</div>

<form id="bulk-delete-form" method="POST" action="{{ route('developer.management-munfiq.bulk-delete') }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.row-checkbox');
        const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
        const bulkDeleteForm = document.getElementById('bulk-delete-form');

        function toggleBulkDeleteBtn() {
            const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
            if (checkedBoxes.length > 0) {
                bulkDeleteBtn.classList.remove('hidden');
            } else {
                bulkDeleteBtn.classList.add('hidden');
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                toggleBulkDeleteBtn();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                toggleBulkDeleteBtn();
                if (!this.checked) selectAll.checked = false;
                if (document.querySelectorAll('.row-checkbox:checked').length === checkboxes.length && checkboxes.length > 0) {
                    selectAll.checked = true;
                }
            });
        });

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
    });
</script>
@endsection
