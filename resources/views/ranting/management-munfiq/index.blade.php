@extends('layouts.app')

@section('page_title', 'Kelola Data Munfiq')
@section('page_subtitle', 'Manajemen data munfiq dan kode kaleng.')

@section('content')
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="relative w-full sm:w-96">
        <form method="GET" action="{{ route('ranting.management-munfiq.index') }}">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, kode kaleng, atau ranting..." class="w-full rounded-2xl border border-zinc-200 bg-white px-4 py-2.5 text-sm outline-none ring-0 placeholder:text-zinc-400 focus:border-green-300">
            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-green-600">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

</div>

<div class="rounded-3xl border border-zinc-200 bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left text-sm text-zinc-600">
            <thead class="bg-zinc-50 border-b border-zinc-200 text-zinc-500 uppercase tracking-wider text-xs font-semibold">
                <tr>
                    <th class="px-6 py-4">No</th>
                    <th class="px-6 py-4">Kode Kaleng</th>
                    <th class="px-6 py-4">Nama Munfiq</th>
                    
                    <th class="px-6 py-4">JK</th>
                    <th class="px-6 py-4">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse($munfiqs as $index => $munfiq)
                    <tr class="hover:bg-zinc-50/50 transition-colors">
                        <td class="px-6 py-4">{{ $munfiqs->firstItem() + $index }}</td>
                        <td class="px-6 py-4 font-mono font-medium text-zinc-800">{{ $munfiq->kode_kaleng }}</td>
                        <td class="px-6 py-4 font-medium text-zinc-800">{{ $munfiq->nama }}</td>
                        
                        <td class="px-6 py-4">{{ $munfiq->jenis_kelamin }}</td>
                        <td class="px-6 py-4">
                            @if($munfiq->status === 'Aktif')
                                <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">Aktif</span>
                            @else
                                <span class="rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-semibold text-zinc-600">Pasif</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-zinc-500">
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


@endsection
