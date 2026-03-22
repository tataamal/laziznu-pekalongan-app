@extends('layouts.app')

@section('page_title', 'Edit Data Ranting')
@section('page_subtitle', 'Edit informasi ranting yang ada.')

@section('content')
<div class="max-w-3xl rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm sm:p-8">
    <form action="{{ route('developer.management-ranting.update', $dataRanting->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label for="wilayah_id" class="block text-sm font-medium text-zinc-700">Wilayah <span class="text-red-500">*</span></label>
            <select name="wilayah_id" id="wilayah_id" class="mt-1 block w-full rounded-2xl border border-zinc-200 bg-white px-4 py-2.5 text-sm outline-none ring-0 focus:border-green-300" required>
                <option value="">Pilih Wilayah</option>
                @foreach($wilayahs as $w)
                    <option value="{{ $w->id }}" {{ (old('wilayah_id', $dataRanting->wilayah_id) == $w->id) ? 'selected' : '' }}>{{ $w->nama_wilayah }}</option>
                @endforeach
            </select>
            @error('wilayah_id') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="nama" class="block text-sm font-medium text-zinc-700">Nama Ranting <span class="text-red-500">*</span></label>
            <input type="text" name="nama" id="nama" value="{{ old('nama', $dataRanting->nama) }}" class="mt-1 block w-full rounded-2xl border border-zinc-200 bg-white px-4 py-2.5 text-sm outline-none ring-0 placeholder:text-zinc-400 focus:border-green-300" required>
            @error('nama') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="kode_ranting" class="block text-sm font-medium text-zinc-700">Kode Ranting <span class="text-red-500">*</span></label>
            <input type="text" name="kode_ranting" id="kode_ranting" value="{{ old('kode_ranting', $dataRanting->kode_ranting) }}" class="mt-1 block w-full rounded-2xl border border-zinc-200 bg-white px-4 py-2.5 text-sm outline-none ring-0 placeholder:text-zinc-400 focus:border-green-300" required>
            @error('kode_ranting') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="rounded-2xl bg-green-700 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800">
                Simpan Perubahan
            </button>
            <a href="{{ route('developer.management-ranting.index') }}" class="rounded-2xl border border-zinc-200 bg-white px-6 py-2.5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
