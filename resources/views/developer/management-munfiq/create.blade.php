@extends('layouts.app')

@section('page_title', 'Tambah Data Munfiq')
@section('page_subtitle', 'Masukkan informasi munfiq baru secara manual atau melalui file Excel.')

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <!-- Form Manual -->
    <div class="lg:col-span-2 rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm sm:p-8">
        <h3 class="mb-6 text-lg font-bold text-zinc-800 border-b border-zinc-100 pb-4">Input Manual</h3>
        <form action="{{ route('developer.management-munfiq.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="data_ranting_id" class="block text-sm font-medium text-zinc-700">Ranting <span class="text-red-500">*</span></label>
                <select name="data_ranting_id" id="data_ranting_id" class="mt-1 block w-full rounded-2xl border border-zinc-200 bg-white px-4 py-2.5 text-sm outline-none ring-0 focus:border-green-300" required>
                    <option value="">Pilih Ranting</option>
                    @foreach($rantings as $ranting)
                        <option value="{{ $ranting->id }}" {{ old('data_ranting_id') == $ranting->id ? 'selected' : '' }}>{{ $ranting->nama }} (Kode: {{ $ranting->kode_ranting }})</option>
                    @endforeach
                </select>
                @error('data_ranting_id') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="nama" class="block text-sm font-medium text-zinc-700">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="nama" id="nama" value="{{ old('nama') }}" class="mt-1 block w-full rounded-2xl border border-zinc-200 bg-white px-4 py-2.5 text-sm outline-none ring-0 placeholder:text-zinc-400 focus:border-green-300" placeholder="Masukkan nama" required>
                @error('nama') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="jenis_kelamin" class="block text-sm font-medium text-zinc-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                <select name="jenis_kelamin" id="jenis_kelamin" class="mt-1 block w-full rounded-2xl border border-zinc-200 bg-white px-4 py-2.5 text-sm outline-none ring-0 focus:border-green-300" required>
                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-Laki (L)</option>
                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                </select>
                @error('jenis_kelamin') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="alamat" class="block text-sm font-medium text-zinc-700">Alamat Lengkap</label>
                <textarea name="alamat" id="alamat" rows="3" class="mt-1 block w-full rounded-2xl border border-zinc-200 bg-white px-4 py-2.5 text-sm outline-none ring-0 placeholder:text-zinc-400 focus:border-green-300" placeholder="RT/RW, Desa, dsj.">{{ old('alamat') }}</textarea>
                @error('alamat') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-zinc-700">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status" class="mt-1 block w-full rounded-2xl border border-zinc-200 bg-white px-4 py-2.5 text-sm outline-none ring-0 focus:border-green-300" required>
                    <option value="Aktif" {{ old('status', 'Aktif') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Pasif" {{ old('status') == 'Pasif' ? 'selected' : '' }}>Pasif</option>
                </select>
                @error('status') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="submit" class="rounded-2xl bg-green-700 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800">
                    Simpan
                </button>
                <a href="{{ route('developer.management-munfiq.index') }}" class="rounded-2xl border border-zinc-200 bg-white px-6 py-2.5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <!-- Form Import Excel -->
    <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm sm:p-8 h-fit">
        <h3 class="mb-6 text-lg font-bold text-zinc-800 border-b border-zinc-100 pb-4">Import dari Excel</h3>
        
        <div class="mb-6 rounded-2xl bg-blue-50/50 p-4 border border-blue-100">
            <h4 class="text-sm font-semibold text-blue-800 mb-3 flex items-center gap-2">
                <i class="fas fa-info-circle"></i> Petunjuk Import:
            </h4>
            <ol class="list-decimal list-inside text-xs text-blue-800/80 space-y-2 leading-relaxed">
                <li>Download template Excel menggunakan tombol di bawah.</li>
                <li>Isi data sesuai kolom yang tersedia (Ranting, Nama, Jenis Kelamin, Alamat, Status).</li>
                <li>Pastikan <strong>Nama Ranting</strong> diketik lengkap dan sesuai dengan data ranting di sistem.</li>
                <li>Upload file Excel yang sudah diisi (.xlsx atau .csv maksimal 5MB).</li>
            </ol>
        </div>

        <form action="{{ route('developer.management-munfiq.import') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div>
                <label for="file" class="block text-sm font-medium text-zinc-700 mb-2">Pilih File Excel/CSV <span class="text-red-500">*</span></label>
                <input type="file" name="file" id="file" accept=".xlsx, .xls, .csv" class="block w-full text-sm text-zinc-500 file:mr-4 file:rounded-xl file:border-0 file:bg-green-50 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-green-700 hover:file:bg-green-100 focus:outline-none" required>
                @error('file') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            
            <div class="flex flex-col gap-3 pt-4 border-t border-zinc-100">
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-green-700 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800">
                    <i class="fas fa-upload"></i> Import Data Utama
                </button>
                <a href="{{ route('developer.management-munfiq.template') }}" class="flex w-full items-center justify-center gap-2 rounded-2xl border border-zinc-200 bg-white px-6 py-2.5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50">
                    <i class="fas fa-download"></i> Download Template
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
