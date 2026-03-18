@extends('layouts.app')

@section('page_title', 'Call Center')
@section('page_subtitle', 'Informasi layanan bantuan dan kontak Pimpinan Cabang.')

@section('content')
    <div class="w-full space-y-8">
        {{-- Hero / Intro --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-6 md:px-8 md:py-8 bg-slate-50/60 border-b border-slate-100">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 px-3 py-1 text-xs font-medium mb-4">
                        Layanan Bantuan Pimpinan Cabang
                    </div>

                    <h2 class="text-2xl md:text-3xl font-semibold text-slate-800 leading-tight">
                        Call Center PC
                    </h2>

                    <p class="mt-3 text-sm md:text-base text-slate-500 leading-7">
                        Layanan bantuan pusat untuk seluruh pengelola MWC dan Ranting. Hubungi kami untuk koordinasi tingkat cabang, kendala sistem yang mendesak, atau informasi program PC.
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
                        <p class="mt-2 text-sm text-slate-500">Kontak langsung pusat Pimpinan Cabang.</p>
                        <div class="mt-4">
                            <a href="tel:+622188899900"
                               class="text-sm font-semibold text-emerald-700 hover:text-emerald-800">
                                +62 21 888 999 00
                            </a>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 hover:shadow-sm transition">
                        <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-lg mb-4">
                            ✉️
                        </div>
                        <h3 class="text-sm font-semibold text-slate-800">Email</h3>
                        <p class="mt-2 text-sm text-slate-500">Layanan korespondensi resmi tingkat PC.</p>
                        <div class="mt-4">
                            <a href="mailto:admin@pc-laziznu.org"
                               class="text-sm font-semibold text-blue-700 hover:text-blue-800">
                                admin@pc-laziznu.org
                            </a>
                        </div>
                    </div>

                    {{-- Jam Layanan --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 hover:shadow-sm transition">
                        <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-lg mb-4">
                            ⏰
                        </div>
                        <h3 class="text-sm font-semibold text-slate-800">Jam Layanan</h3>
                        <p class="mt-2 text-sm text-slate-500">Waktu operasional sekretariat Cabang.</p>
                        <div class="mt-4 text-sm font-medium text-slate-700 leading-6">
                            Senin - Sabtu<br>
                            09:00 - 16:00 WIB
                        </div>
                    </div>

                    {{-- Alamat --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 hover:shadow-sm transition">
                        <div class="w-11 h-11 rounded-xl bg-violet-50 border border-violet-100 flex items-center justify-center text-lg mb-4">
                            📍
                        </div>
                        <h3 class="text-sm font-semibold text-slate-800">Alamat</h3>
                        <p class="mt-2 text-sm text-slate-500">Kantor Pimpinan Cabang LAZISNU.</p>
                        <div class="mt-4 text-sm font-medium text-slate-700 leading-6">
                            Gedung PCNU Lt. 2,<br>
                            Pusat Kota Pekalongan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
