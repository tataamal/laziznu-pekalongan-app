@extends('layouts.app')

@section('page_title', 'Manajemen User')
@section('page_subtitle', 'Kelola data pengguna, hak akses, dan import data massal.')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-1 items-center gap-2">
            <form action="{{ route('developer.users.index') }}" method="GET" class="relative w-full max-w-sm">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama, email, telpon, role..." class="w-full rounded-2xl border border-zinc-200 bg-white py-2.5 pl-10 pr-4 text-sm outline-none transition placeholder:text-zinc-400 focus:border-green-500 focus:ring-1 focus:ring-green-500">
            </form>

            <div id="bulkActions" class="hidden flex items-center gap-2">
                <button onclick="document.getElementById('bulkWilayahModal').classList.remove('hidden')" class="rounded-2xl border border-zinc-200 bg-white px-4 py-2.5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50">
                    <i class="fas fa-edit mr-1 text-blue-600"></i> Edit Wilayah <span class="bg-zinc-100 px-1.5 py-0.5 rounded-full text-xs" id="bulkEditCount">0</span>
                </button>
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
                <i class="fas fa-plus mr-2"></i> Tambah User
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
                    <th class="px-5 py-4 font-semibold">Nama</th>
                    <th class="px-5 py-4 font-semibold">Kontak</th>
                    <th class="px-5 py-4 font-semibold">Role</th>
                    <th class="px-5 py-4 font-semibold">Wilayah</th>
                    <th class="px-5 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse($users as $user)
                    <tr class="text-sm text-zinc-700 transition hover:bg-zinc-50/50">
                        <td class="px-5 py-4 text-center">
                            <input type="checkbox" value="{{ $user->id }}" class="user-checkbox h-4 w-4 rounded border-zinc-300 text-green-600 focus:ring-green-500 cursor-pointer" onchange="toggleBulkActions()">
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-100 font-bold text-green-700">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-medium text-zinc-900">{{ $user->name }} {{ $user->id === auth()->id() ? '(Anda)' : '' }}</div>
                                    <div class="text-xs text-zinc-500">Bergabung {{ $user->created_at->format('d M Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div>{{ $user->email }}</div>
                            <div class="text-xs text-zinc-500">{{ $user->telpon ?? '-' }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                                {{ $user->role === 'developer' ? 'bg-purple-100 text-purple-700' : '' }}
                                {{ $user->role === 'pc' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $user->role === 'mwc' ? 'bg-orange-100 text-orange-700' : '' }}
                                {{ $user->role === 'ranting' ? 'bg-green-100 text-green-700' : '' }}
                            ">
                                {{ strtoupper($user->role) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">{{ $user->wilayah->nama_wilayah ?? '-' }}</td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="openEditModal({{ $user->toJson() }})" class="rounded-xl p-2 text-zinc-400 transition hover:bg-zinc-100 hover:text-blue-600">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="openDeleteModal({{ $user->id }})" class="rounded-xl p-2 text-zinc-400 transition hover:bg-zinc-100 hover:text-red-600" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-sm text-zinc-500">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i class="fas fa-users text-4xl text-zinc-300"></i>
                                <span>Belum ada data pengguna ditemukan.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($users->hasPages())
            <div class="border-t border-zinc-100 p-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Create User -->
    <div id="createModal" class="fixed inset-0 z-50 flex hidden items-center justify-center bg-zinc-900/50 px-4 backdrop-blur-sm transition-opacity">
        <div class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-xl sm:p-8">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-zinc-900">Tambah User Baru</h3>
                <button onclick="document.getElementById('createModal').classList.add('hidden')" class="text-zinc-400 hover:text-zinc-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('developer.users.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="mb-1 block text-sm font-medium text-zinc-700">Nama</label>
                    <input type="text" name="name" required class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm outline-none focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-zinc-700">Email</label>
                    <input type="email" name="email" required class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm outline-none focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-zinc-700">Password</label>
                    <input type="password" name="password" required class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm outline-none focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-zinc-700">Role</label>
                        <select name="role" required class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm outline-none focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                            <option value="">Pilih Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role }}">{{ strtoupper($role) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-zinc-700">No. Telpon</label>
                        <input type="text" name="telpon" class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm outline-none focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-zinc-700">Wilayah</label>
                    <select name="wilayah_id" class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm outline-none focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                        <option value="">Pilih Wilayah (Opsional)</option>
                        @foreach($wilayahs as $wilayah)
                            <option value="{{ $wilayah->id }}">{{ $wilayah->nama_wilayah }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="rounded-2xl px-5 py-2.5 text-sm font-semibold text-zinc-600 hover:bg-zinc-100">Batal</button>
                    <button type="submit" class="rounded-2xl bg-green-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-800">Simpan User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit User -->
    <div id="editModal" class="fixed inset-0 z-50 flex hidden items-center justify-center bg-zinc-900/50 px-4 backdrop-blur-sm transition-opacity">
        <div class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-xl sm:p-8">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-zinc-900">Edit User</h3>
                <button onclick="document.getElementById('editModal').classList.add('hidden')" class="text-zinc-400 hover:text-zinc-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="mb-1 block text-sm font-medium text-zinc-700">Nama</label>
                    <input type="text" name="name" id="edit_name" required class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm outline-none focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-zinc-700">Email</label>
                    <input type="email" name="email" id="edit_email" required class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm outline-none focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-zinc-700">Password Baru</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password" class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm outline-none focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-zinc-700">Role</label>
                        <select name="role" id="edit_role" required class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm outline-none focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                            @foreach($roles as $role)
                                <option value="{{ $role }}">{{ strtoupper($role) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-zinc-700">No. Telpon</label>
                        <input type="text" name="telpon" id="edit_telpon" class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm outline-none focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-zinc-700">Wilayah</label>
                    <select name="wilayah_id" id="edit_wilayah_id" class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm outline-none focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                        <option value="">Pilih Wilayah (Opsional)</option>
                        @foreach($wilayahs as $wilayah)
                            <option value="{{ $wilayah->id }}">{{ $wilayah->nama_wilayah }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="rounded-2xl px-5 py-2.5 text-sm font-semibold text-zinc-600 hover:bg-zinc-100">Batal</button>
                    <button type="submit" class="rounded-2xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Perbarui User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Delete User -->
    <div id="deleteModal" class="fixed inset-0 z-50 flex hidden items-center justify-center bg-zinc-900/50 px-4 backdrop-blur-sm transition-opacity">
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-xl sm:p-8 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-600">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>
            <h3 class="mb-2 text-lg font-semibold text-zinc-900">Hapus Pengguna</h3>
            <p class="mb-6 text-sm text-zinc-500">Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.</p>
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
                <h3 class="text-lg font-semibold text-zinc-900">Import Data User</h3>
                <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-zinc-400 hover:text-zinc-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-6 rounded-2xl bg-blue-50 p-4 text-sm text-blue-700 border border-blue-200">
                <p class="font-medium mb-1"><i class="fas fa-info-circle mr-1"></i> Format Import</p>
                <p class="text-xs">Pastikan file Excel (.xlsx) Anda memiliki header kolom: <b>nama, email, password, role, telpon, wilayah_id</b>. Role harus berisi 'developer', 'pc', 'mwc', atau 'ranting'.</p>
                <a href="{{ route('developer.users.template') }}" class="mt-3 inline-flex items-center gap-2 rounded-xl bg-white px-3 py-1.5 text-xs font-semibold text-blue-700 shadow-sm border border-blue-200 hover:bg-blue-100 transition">
                    <i class="fas fa-download"></i> Download Template File
                </a>
            </div>
            <form action="{{ route('developer.users.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
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

    <!-- Modal Bulk Edit Wilayah -->
    <div id="bulkWilayahModal" class="fixed inset-0 z-50 flex hidden items-center justify-center bg-zinc-900/50 px-4 backdrop-blur-sm transition-opacity">
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-xl sm:p-8">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-zinc-900">Ubah Wilayah Massal</h3>
                <button onclick="document.getElementById('bulkWilayahModal').classList.add('hidden')" class="text-zinc-400 hover:text-zinc-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p class="mb-4 text-sm text-zinc-600">Pilih wilayah baru untuk diterapkan pada pengguna yang dicentang.</p>
            <form id="bulkWilayahForm" action="{{ route('developer.users.bulk-update-wilayah') }}" method="POST">
                @csrf
                <div id="bulkWilayahInputs"></div>
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-zinc-700">Wilayah Baru</label>
                    <select name="wilayah_id" class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm outline-none focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                        <option value="">Kosongkan Wilayah</option>
                        @foreach($wilayahs as $wilayah)
                            <option value="{{ $wilayah->id }}">{{ $wilayah->nama_wilayah }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('bulkWilayahModal').classList.add('hidden')" class="rounded-2xl px-5 py-2.5 text-sm font-semibold text-zinc-600 hover:bg-zinc-100">Batal</button>
                    <button type="submit" class="rounded-2xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Terapkan</button>
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
            <p class="mb-6 text-sm text-zinc-500">Apakah Anda yakin ingin menghapus puluhan pengguna yang dipilih? Tindakan ini permanen.</p>
            <form id="bulkDeleteForm" action="{{ route('developer.users.bulk-delete') }}" method="POST" class="flex justify-center gap-3">
                @csrf
                <div id="bulkDeleteInputs"></div>
                <button type="button" onclick="document.getElementById('bulkDeleteModal').classList.add('hidden')" class="rounded-2xl border border-zinc-200 bg-white px-5 py-2.5 text-sm font-semibold text-zinc-700 hover:bg-zinc-50">Batal</button>
                <button type="submit" class="rounded-2xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Ya, Hapus Semua</button>
            </form>
        </div>
    </div>

    <script>
        const selectAllCheckbox = document.getElementById('selectAll');
        const userCheckboxes = document.querySelectorAll('.user-checkbox');
        const bulkActions = document.getElementById('bulkActions');
        const bulkEditCount = document.getElementById('bulkEditCount');
        const bulkDeleteCount = document.getElementById('bulkDeleteCount');
        const bulkWilayahInputsContainer = document.getElementById('bulkWilayahInputs');
        const bulkDeleteInputsContainer = document.getElementById('bulkDeleteInputs');

        function toggleBulkActions() {
            let checkedCount = 0;
            let inputsHtml = '';
            
            userCheckboxes.forEach(cb => {
                if(cb.checked) {
                    checkedCount++;
                    inputsHtml += `<input type="hidden" name="user_ids[]" value="${cb.value}">`;
                }
            });

            if (checkedCount > 0) {
                bulkActions.classList.remove('hidden');
                bulkEditCount.innerText = checkedCount;
                bulkDeleteCount.innerText = checkedCount;
                bulkWilayahInputsContainer.innerHTML = inputsHtml;
                bulkDeleteInputsContainer.innerHTML = inputsHtml;
            } else {
                bulkActions.classList.add('hidden');
            }

            // Sync selectAll checkbox
            if (userCheckboxes.length > 0) {
                selectAllCheckbox.checked = (checkedCount === userCheckboxes.length);
            }
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                userCheckboxes.forEach(cb => {
                    cb.checked = selectAllCheckbox.checked;
                });
                toggleBulkActions();
            });
        }
        function openEditModal(user) {
            const form = document.getElementById('editForm');
            form.action = `/developer/users/${user.id}`;
            document.getElementById('edit_name').value = user.name;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_role').value = user.role;
            document.getElementById('edit_telpon').value = user.telpon || '';
            document.getElementById('edit_wilayah_id').value = user.wilayah_id || '';
            document.getElementById('editModal').classList.remove('hidden');
        }

        function openDeleteModal(id) {
            const form = document.getElementById('deleteForm');
            form.action = `/developer/users/${id}`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }
    </script>
@endsection
