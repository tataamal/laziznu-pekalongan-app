@extends('layouts.app')

@section('page_title', 'Call Center')
@section('page_subtitle', 'Informasi layanan bantuan dan kontak MWC.')

@section('content')
    <div class="w-full space-y-8">
        {{-- Hero / Intro --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-6 md:px-8 md:py-8 bg-slate-50/60 border-b border-slate-100">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 px-3 py-1 text-xs font-medium mb-4">
                        Layanan Bantuan
                    </div>

                    <h2 class="text-2xl md:text-3xl font-semibold text-slate-800 leading-tight">
                        Call Center MWC
                    </h2>

                    <p class="mt-3 text-sm md:text-base text-slate-500 leading-7">
                        Kami siap membantu Anda untuk pertanyaan, kendala sistem, maupun kebutuhan informasi lainnya.
                        Silakan hubungi kami melalui kontak di bawah ini pada jam layanan yang tersedia.
                    </p>
                </div>
            </div>

            <div class="p-6 md:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
                    {{-- Telepon --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 hover:shadow-sm transition">
                        <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-lg mb-4">
                            📞
                        </div>
                        <h3 class="text-sm font-semibold text-slate-800">Telepon</h3>
                        <p class="mt-2 text-sm text-slate-500">Hubungi call center secara langsung.</p>
                        <div class="mt-4">
                            @if(auth()->user()->telpon)
                                <a href="tel:{{ auth()->user()->telpon }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-800">
                                    {{ auth()->user()->telpon }}
                                </a>
                            @else
                                <span class="text-sm font-semibold text-slate-500">-</span>
                            @endif
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 hover:shadow-sm transition">
                        <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-lg mb-4">
                            ✉️
                        </div>
                        <h3 class="text-sm font-semibold text-slate-800">Email</h3>
                        <p class="mt-2 text-sm text-slate-500">Kirim pertanyaan atau laporan kendala.</p>
                        <div class="mt-4">
                            <a href="mailto:{{ auth()->user()->email }}" class="text-sm font-semibold text-blue-700 hover:text-blue-800">
                                {{ auth()->user()->email }}
                            </a>
                        </div>
                    </div>

                    {{-- Jam Layanan --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 hover:shadow-sm transition">
                        <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-lg mb-4">
                            ⏰
                        </div>
                        <h3 class="text-sm font-semibold text-slate-800">Jam Layanan</h3>
                        <p class="mt-2 text-sm text-slate-500">Waktu operasional bantuan dan respon admin.</p>
                        <div class="mt-4 text-sm font-medium text-slate-700 leading-6">
                            Senin - Jumat<br>
                            08:00 - 17:00 WIB
                        </div>
                    </div>

                    {{-- Alamat --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 hover:shadow-sm transition">
                        <div class="w-11 h-11 rounded-xl bg-violet-50 border border-violet-100 flex items-center justify-center text-lg mb-4">
                            📍
                        </div>
                        <h3 class="text-sm font-semibold text-slate-800">Alamat</h3>
                        <p class="mt-2 text-sm text-slate-500">Lokasi kantor layanan MWC.</p>
                        <div class="mt-4 text-sm font-medium text-slate-700 leading-6 whitespace-pre-line">
                            {{ auth()->user()->wilayah->alamat ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Additional help section --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/60">
                <h3 class="text-lg font-semibold text-slate-800">
                    Informasi Layanan
                </h3>
                <p class="text-sm text-slate-500 mt-1">
                    Gunakan kanal bantuan yang sesuai agar penanganan lebih cepat.
                </p>
            </div>

            <div class="p-6 md:p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-5">
                        <h4 class="text-sm font-semibold text-slate-800">Bantuan Cepat</h4>
                        <p class="mt-2 text-sm text-slate-500 leading-6">
                            Untuk kendala mendesak, gunakan kontak telepon agar tim dapat membantu lebih cepat.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-5">
                        <h4 class="text-sm font-semibold text-slate-800">Laporan Detail</h4>
                        <p class="mt-2 text-sm text-slate-500 leading-6">
                            Untuk kebutuhan laporan atau penjelasan rinci, gunakan email dan sertakan informasi lengkap.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-5">
                        <h4 class="text-sm font-semibold text-slate-800">Jam Operasional</h4>
                        <p class="mt-2 text-sm text-slate-500 leading-6">
                            Respon layanan mengikuti jam kerja yang berlaku. Pesan di luar jam kerja akan diproses pada hari kerja berikutnya.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection