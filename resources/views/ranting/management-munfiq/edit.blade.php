@extends('layouts.app')

@section('page_title', 'Edit Data Munfiq')
@section('page_subtitle', 'Edit informasi munfiq yang ada.')

@section('content')
<div class="max-w-3xl rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm sm:p-8">
    <form action="{{ route('ranting.management-munfiq.update', $munfiq->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        

        <div>
            <label for="kode_kaleng" class="block text-sm font-medium text-zinc-700">Kode Kaleng <span class="text-red-500">*</span></label>
            <input type="text" name="kode_kaleng" id="kode_kaleng" value="{{ old('kode_kaleng', $munfiq->kode_kaleng) }}" class="mt-1 block w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-2.5 text-sm outline-none ring-0 placeholder:text-zinc-400 focus:border-green-300" required>
            @error('kode_kaleng') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="nama" class="block text-sm font-medium text-zinc-700">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" name="nama" id="nama" value="{{ old('nama', $munfiq->nama) }}" class="mt-1 block w-full rounded-2xl border border-zinc-200 bg-white px-4 py-2.5 text-sm outline-none ring-0 placeholder:text-zinc-400 focus:border-green-300" required>
            @error('nama') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="jenis_kelamin" class="block text-sm font-medium text-zinc-700">Jenis Kelamin <span class="text-red-500">*</span></label>
            <select name="jenis_kelamin" id="jenis_kelamin" class="mt-1 block w-full rounded-2xl border border-zinc-200 bg-white px-4 py-2.5 text-sm outline-none ring-0 focus:border-green-300" required>
                <option value="L" {{ old('jenis_kelamin', $munfiq->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-Laki (L)</option>
                <option value="P" {{ old('jenis_kelamin', $munfiq->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
            </select>
            @error('jenis_kelamin') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="alamat" class="block text-sm font-medium text-zinc-700">Alamat Lengkap</label>
            <textarea name="alamat" id="alamat" rows="3" class="mt-1 block w-full rounded-2xl border border-zinc-200 bg-white px-4 py-2.5 text-sm outline-none ring-0 placeholder:text-zinc-400 focus:border-green-300">{{ old('alamat', $munfiq->alamat) }}</textarea>
            @error('alamat') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="status" class="block text-sm font-medium text-zinc-700">Status <span class="text-red-500">*</span></label>
            <select name="status" id="status" class="mt-1 block w-full rounded-2xl border border-zinc-200 bg-white px-4 py-2.5 text-sm outline-none ring-0 focus:border-green-300" required>
                <option value="Aktif" {{ old('status', $munfiq->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Pasif" {{ old('status', $munfiq->status) == 'Pasif' ? 'selected' : '' }}>Pasif</option>
            </select>
            @error('status') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="rounded-2xl bg-green-700 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800">
                Simpan Perubahan
            </button>
            <a href="{{ route('ranting.management-munfiq.index') }}" class="rounded-2xl border border-zinc-200 bg-white px-6 py-2.5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
