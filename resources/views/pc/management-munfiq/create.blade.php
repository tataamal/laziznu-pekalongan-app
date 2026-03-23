@extends('layouts.app')

@section('page_title', 'Tambah Data Munfiq')
@section('page_subtitle', 'Masukkan informasi munfiq baru. Kode kaleng akan otomatis digenerate.')

@section('content')
<div class="max-w-3xl rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm sm:p-8">
    <form action="{{ route('pc.management-munfiq.store') }}" method="POST" class="space-y-6">
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
            <a href="{{ route('pc.management-munfiq.index') }}" class="rounded-2xl border border-zinc-200 bg-white px-6 py-2.5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
