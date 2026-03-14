@extends('layouts.app')

@section('page_title', 'Dashboard')
@section('page_subtitle', 'Pantau pengguna, aktivitas, dan statistik sistem.')

@section('content')
    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl bg-gradient-to-br from-green-700 to-emerald-500 p-5 text-white shadow-sm">
            <div class="text-sm font-medium text-white/90">Total User Aktif</div>
            <div class="mt-4 text-4xl font-bold">{{ $totalAllUsers }}</div>
            <div class="mt-3 text-xs text-white/80">Total pengguna aktif</div>
        </div>

        <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
            <div class="text-sm font-medium text-zinc-600">User PC</div>
            <div class="mt-4 text-4xl font-bold tracking-tight text-zinc-900">{{ $totalPc }}</div>
            <div class="mt-3 text-xs text-zinc-500">Jumlah user Pimpinan Cabang NU Pekalongan</div>
        </div>

        <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
            <div class="text-sm font-medium text-zinc-600">User MWC</div>
            <div class="mt-4 text-4xl font-bold tracking-tight text-zinc-900">{{ $totalMwc }}</div>
            <div class="mt-3 text-xs text-zinc-500">Jumlah user MWC NU Pekalongan</div>
        </div>

        <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
            <div class="text-sm font-medium text-zinc-600">User Ranting</div>
            <div class="mt-4 text-4xl font-bold tracking-tight text-zinc-900">{{ $totalRanting }}</div>
            <div class="mt-3 text-xs text-zinc-500">Total akun role Ranting</div>
        </div>
    </section>

    <section class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-3">
        <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-zinc-900">Aktivitas Terakhir Anda</h3>
            <div class="mt-4 space-y-4">
                @forelse ($aktivitasUser as $aktivitas)
                    <div class="border-b border-zinc-100 pb-3 last:border-b-0 last:pb-0">
                        <div class="text-sm font-medium text-zinc-800">{{ $aktivitas->description ?? ucfirst($aktivitas->action) }}</div>
                        <div class="mt-1 flex items-center justify-between text-xs text-zinc-500">
                            <span>{{ $aktivitas->created_at->diffForHumans() }}</span>
                            <span class="text-[10px] text-zinc-400">{{ $aktivitas->ip_address }}</span>
                        </div>
                    </div>
                @empty
                    <div class="py-4 text-center text-sm text-zinc-500">
                        Belum ada aktivitas yang tercatat.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm xl:col-span-2">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-sm font-semibold text-zinc-900">Data Transaksi</h3>
                <div class="flex w-full gap-2 sm:w-auto">
                    <input
                        type="text"
                        id="search"
                        placeholder="Cari transaksi..."
                        class="w-full rounded-2xl border border-zinc-200 bg-white px-4 py-2.5 text-sm outline-none ring-0 placeholder:text-zinc-400 focus:border-green-300 sm:w-64"
                    >
                    <button
                        onclick="exportTable()"
                        class="rounded-2xl bg-green-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800"
                    >
                        Export
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="dataTable" class="w-full border-collapse">
                    <thead>
                        <tr class="border-b border-zinc-100 text-left text-xs uppercase tracking-wide text-zinc-500">
                            <th class="px-3 py-3 font-semibold">Kode</th>
                            <th class="px-3 py-3 font-semibold">Tanggal</th>
                            <th class="px-3 py-3 font-semibold">Ranting</th>
                            <th class="px-3 py-3 font-semibold">Jenis</th>
                            <th class="px-3 py-3 font-semibold">Nominal</th>
                            <th class="px-3 py-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transaksiTerbaru as $trx)
                            <tr class="border-b border-zinc-100 text-sm text-zinc-700">
                                <td class="px-3 py-3">{{ $trx['kode'] }}</td>
                                <td class="px-3 py-3">{{ $trx['tanggal'] }}</td>
                                <td class="px-3 py-3">{{ $trx['ranting'] }}</td>
                                <td class="px-3 py-3">{{ $trx['jenis'] }}</td>
                                <td class="px-3 py-3">{{ $trx['nominal'] }}</td>
                                <td class="px-3 py-3">{{ $trx['status'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-6 text-center text-sm text-zinc-500">
                                    Belum ada data transaksi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection