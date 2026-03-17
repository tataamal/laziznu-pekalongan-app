@extends('layouts.landing')

@section('content')
    <header
        class="relative bg-gradient-to-br from-[#014421] via-[#015e2e] to-[#014421] pt-48 pb-32 px-6 text-center overflow-hidden">

        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="0.5" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>
        </div>

        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-green-400/20 blur-[120px] rounded-full pointer-events-none">
        </div>

        <div class="relative z-10 max-w-5xl mx-auto">
            <div class="mb-10 inline-block group">
                <img src="{{ asset('images/logo.png') }}"
                    class="w-[280px] md:w-[350px] mx-auto drop-shadow-[0_20px_50px_rgba(0,0,0,0.3)] animate-float transition-transform duration-700 group-hover:scale-110"
                    alt="Logo LazisNU">
            </div>

            <h1
                class="text-4xl md:text-7xl font-black text-white tracking-tight mb-6 leading-tight drop-shadow-[0_4px_12px_rgba(0,0,0,0.5)]">
                LazisNU <span class="text-green-300">Kabupaten Pekalongan</span>
            </h1>

            <div class="w-24 h-1.5 bg-green-400 mx-auto mb-8 rounded-full shadow-lg shadow-green-400/20"></div>

            <p
                class="text-xl md:text-2xl font-medium text-green-50 max-w-3xl mx-auto leading-relaxed opacity-95 tracking-wide">
                Lembaga Amil Zakat, Infaq, dan Shodaqoh <br class="hidden md:block"> Nahdlatul Ulama
            </p>
        </div>
    </header>

    <style>
        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
    </style>

    <section id="profile" class="py-12 px-6 max-w-7xl mx-auto space-y-10">

        <div
            class="bg-white rounded-3xl p-10 md:p-14 shadow-sm border border-zinc-100 hover:shadow-md transition-all duration-300">
            <div class="flex items-center gap-5 mb-8">
                <div
                    class="flex-shrink-0 bg-green-50 rounded-2xl flex items-center justify-center p-3 border border-green-100">
                    <i class="fa-solid fa-mosque text-3xl md:text-4xl text-green-700"></i>
                </div>
                <h2 class="text-[#014421] text-3xl md:text-4xl font-extrabold tracking-tight">
                    Profil LazisNU PC Kabupaten Pekalongan
                </h2>
            </div>

            <div class="space-y-6 text-zinc-700 leading-relaxed text-lg md:text-xl">
                <p class="font-medium">
                    Lembaga Amil Zakat Infaq dan Shadaqah Nahdlatul Ulama adalah lembaga pada pengurus besar Nahdlatul
                    Ulama’ yang berkhidmah dalam pengelolaan zakat, infaq dan shadaqahuntuk kemaslahatan umat.
                </p>

                <div class="bg-zinc-50 border-l-4 border-green-600 p-6 rounded-r-2xl">
                    <p class="text-sm md:text-base text-zinc-600 italic mb-2">
                        Disahkan melalui SK PBNU No: 14/A.II.04/6/2010 dan SK Menteri Agama RI no. 65 Tahun 2005 sebagai
                        Lembaga Amil Zakat Nasional.
                    </p>
                    <p class="font-bold text-green-900 text-lg">
                        — Ketua: H. Mujahidin, S.H.
                    </p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            <div
                class="bg-white p-10 rounded-3xl shadow-sm border border-zinc-100 hover:border-green-100 transition duration-300">
                <div class="flex items-center gap-4 mb-6">
                    <div
                        class="w-12 h-12 bg-green-700 rounded-xl flex items-center justify-center shadow-lg shadow-green-100">
                        <i class="fas fa-eye text-white text-xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-zinc-800 uppercase tracking-widest">Visi</h3>
                </div>
                <p
                    class="text-zinc-600 leading-relaxed text-lg md:text-xl italic font-serif italic border-l-2 border-zinc-200 pl-6">
                    "Bertekad menjadi lembaga pengelola dana masyarakat yang didayagunakan secara amanah dan profesional
                    untuk pemandirian umat."
                </p>
            </div>

            <div
                class="bg-white p-10 rounded-3xl shadow-sm border border-zinc-100 hover:border-green-100 transition duration-300">
                <div class="flex items-center gap-4 mb-6">
                    <div
                        class="w-12 h-12 bg-green-700 rounded-xl flex items-center justify-center shadow-lg shadow-green-100">
                        <i class="fas fa-bullseye text-white text-xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-zinc-800 uppercase tracking-widest">Misi</h3>
                </div>
                <ul class="space-y-5">
                    <li class="flex gap-4 items-start">
                        <span
                            class="flex-shrink-0 w-7 h-7 bg-green-100 text-green-700 rounded-lg flex items-center justify-center text-sm font-black">1</span>
                        <p class="text-zinc-600 text-base md:text-lg leading-snug">Mendorong tumbuhnya kesadaran masyarakat
                            untuk berzakat, infaq, dan sedekah secara rutin.</p>
                    </li>
                    <li class="flex gap-4 items-start">
                        <span
                            class="flex-shrink-0 w-7 h-7 bg-green-100 text-green-700 rounded-lg flex items-center justify-center text-sm font-black">2</span>
                        <p class="text-zinc-600 text-base md:text-lg leading-snug">Mengumpulkan/menghimpun dan
                            mendayagunakan dana zakat, infaq dan sedekah secara profesional, transparan, tepat guna dan
                            tepat sasaran</p>
                    </li>
                    <li class="flex gap-4 items-start">
                        <span
                            class="flex-shrink-0 w-7 h-7 bg-green-100 text-green-700 rounded-lg flex items-center justify-center text-sm font-black">3</span>
                        <p class="text-zinc-600 text-base md:text-lg leading-snug">Menyelenggarakan program pemberdayaan
                            masyarakat guna mengatasi problem kemiskinan, pengangguran dan minimnya akses pendidikan yang
                            layak.</p>
                    </li>
                </ul>
            </div>

        </div>
    </section>
    <section id="pilar" class="py-12 px-6 max-w-7xl mx-auto">
        <div class="mb-10 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-700 rounded-xl flex items-center justify-center shadow-lg shadow-green-100">
                <i class="fas fa-hand-holding-heart text-white text-xl"></i>
            </div>
            <h2 class="text-[#014421] text-3xl md:text-4xl font-extrabold tracking-tight">
                5 Pilar LAZISNU
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <div
                class="bg-white p-8 rounded-3xl shadow-sm border border-zinc-100 hover:border-green-200 hover:shadow-md transition-all duration-300 flex flex-col">
                <div class="flex items-center gap-3 mb-4">
                    <span
                        class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-full uppercase tracking-widest">Pendidikan</span>
                </div>
                <h4 class="text-xl font-bold text-zinc-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-graduation-cap text-green-600"></i>
                    NU CARE - CERDAS
                </h4>
                <p class="text-zinc-600 text-sm leading-relaxed">
                    Peningkatan SDM dan kader melalui beasiswa, pelatihan, dan penguatan fasilitas pendidikan dari tingkat
                    dasar hingga perguruan tinggi.
                </p>
            </div>

            <div
                class="bg-white p-8 rounded-3xl shadow-sm border border-zinc-100 hover:border-green-200 hover:shadow-md transition-all duration-300 flex flex-col">
                <div class="flex items-center gap-3 mb-4">
                    <span
                        class="px-3 py-1 bg-red-50 text-red-700 text-xs font-bold rounded-full uppercase tracking-widest">Kesehatan</span>
                </div>
                <h4 class="text-xl font-bold text-zinc-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-heartbeat text-green-600"></i>
                    NU CARE - SEHAT
                </h4>
                <p class="text-zinc-600 text-sm leading-relaxed">
                    Layanan kesehatan masyarakat bagi keluarga kurang mampu melalui tindakan kuratif maupun pencegahan
                    (preventif).
                </p>
            </div>

            <div
                class="bg-white p-8 rounded-3xl shadow-sm border border-zinc-100 hover:border-green-200 hover:shadow-md transition-all duration-300 flex flex-col">
                <div class="flex items-center gap-3 mb-4">
                    <span
                        class="px-3 py-1 bg-yellow-50 text-yellow-700 text-xs font-bold rounded-full uppercase tracking-widest">Ekonomi</span>
                </div>
                <h4 class="text-xl font-bold text-zinc-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-chart-line text-green-600"></i>
                    NU CARE - BERDAYA
                </h4>
                <p class="text-zinc-600 text-sm leading-relaxed">
                    Mendorong kemandirian dan kesejahteraan melalui semangat kewirausahaan dan pengembangan ekonomi umat.
                </p>
            </div>

            <div
                class="bg-white p-8 rounded-3xl shadow-sm border border-zinc-100 hover:border-green-200 hover:shadow-md transition-all duration-300 flex flex-col">
                <div class="flex items-center gap-3 mb-4">
                    <span
                        class="px-3 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-full uppercase tracking-widest">Lingkungan</span>
                </div>
                <h4 class="text-xl font-bold text-zinc-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-leaf text-green-600"></i>
                    NU CARE - HIJAU
                </h4>
                <p class="text-zinc-600 text-sm leading-relaxed">
                    Pemeliharaan lingkungan, pemanfaatan SDA secara bijaksana, serta penanggulangan dan penanganan bencana
                    alam.
                </p>
            </div>

            <div
                class="bg-white p-8 rounded-3xl shadow-sm border border-zinc-100 hover:border-green-200 hover:shadow-md transition-all duration-300 flex flex-col lg:col-span-1">
                <div class="flex items-center gap-3 mb-4">
                    <span
                        class="px-3 py-1 bg-purple-50 text-purple-700 text-xs font-bold rounded-full uppercase tracking-widest">Sosial</span>
                </div>
                <h4 class="text-xl font-bold text-zinc-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-dove text-green-600"></i>
                    NU CARE - DAMAI
                </h4>
                <p class="text-zinc-600 text-sm leading-relaxed">
                    Layanan sosial dengan semangat dakwah Islam damai (washatiyah) dalam bentuk bantuan sosial sistematik
                    dan kemanusiaan.
                </p>
            </div>

        </div>
    </section>

    <section id="rekap-perolehan" class="py-12 px-6 max-w-7xl mx-auto">
        <div class="mb-8 flex items-center gap-4">
            <div class="w-12 h-12  bg-emerald-700 rounded-xl flex items-center justify-center shadow-lg shadow-green-100">
                <i class="fas fa-file-invoice-dollar text-white text-xl"></i>
            </div>
            <h2 class="text-emerald-900 text-3xl md:text-4xl font-extrabold tracking-tight">
                Rekapitulasi Perolehan
            </h2>
        </div>

        <div
            class="bg-white rounded-3xl shadow-sm border border-zinc-100 overflow-hidden hover:shadow-md transition-all duration-300">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-green-50/50 border-b border-green-100">
                            <th class="px-8 py-6 text-[#014421] font-bold uppercase text-sm tracking-wider">Bulan</th>
                            <th class="px-8 py-6 text-[#014421] font-bold uppercase text-sm tracking-wider">Jumlah (Rp)
                            </th>
                            <th class="px-8 py-6 text-[#014421] font-bold uppercase text-sm tracking-wider text-right">
                                Sumber Dana</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        <tr class="hover:bg-green-50/30 transition-colors group">
                            <td class="px-8 py-5 text-zinc-700 font-semibold text-lg md:text-xl">Januari</td>
                            <td class="px-8 py-5 text-zinc-600 font-mono text-lg md:text-xl font-medium">100.000.000</td>
                            <td class="px-8 py-5 text-right">
                                <span
                                    class="inline-block px-4 py-1.5 bg-green-100 text-green-800 rounded-full text-xs font-bold uppercase tracking-wide group-hover:bg-[#014421] group-hover:text-white transition-all">Zakat</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-green-50/30 transition-colors group">
                            <td class="px-8 py-5 text-zinc-700 font-semibold text-lg md:text-xl">Februari</td>
                            <td class="px-8 py-5 text-zinc-600 font-mono text-lg md:text-xl font-medium">120.000.000</td>
                            <td class="px-8 py-5 text-right">
                                <span
                                    class="inline-block px-4 py-1.5 bg-emerald-100 text-emerald-800 rounded-full text-xs font-bold uppercase tracking-wide group-hover:bg-emerald-600 group-hover:text-white transition-all">Infaq</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-green-50/30 transition-colors group">
                            <td class="px-8 py-5 text-zinc-700 font-semibold text-lg md:text-xl">Maret</td>
                            <td class="px-8 py-5 text-zinc-600 font-mono text-lg md:text-xl font-medium">150.000.000</td>
                            <td class="px-8 py-5 text-right">
                                <span
                                    class="inline-block px-4 py-1.5 bg-teal-100 text-teal-800 rounded-full text-xs font-bold uppercase tracking-wide group-hover:bg-[#014421] group-hover:text-white transition-all">Shodaqoh</span>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-[#014421]">
                        <tr>
                            <td class="px-8 py-5 text-white font-bold text-xl">Total Keseluruhan</td>
                            <td class="px-8 py-5 text-white font-mono text-2xl font-bold" colspan="2">Rp 370.000.000
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>

    <section id="rekap-pentasarufan" class="py-12 px-6 max-w-7xl mx-auto">
        <div class="mb-8 flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-700 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-100">
                <i class="fas fa-hand-holding-heart text-white text-xl"></i>
            </div>
            <h2 class="text-emerald-900 text-3xl md:text-4xl font-extrabold tracking-tight">
                Rekapitulasi Pentasarufan
            </h2>
        </div>

        <div
            class="bg-white rounded-3xl shadow-sm border border-zinc-100 overflow-hidden hover:shadow-md transition-all duration-300">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-emerald-50/50 border-b border-emerald-100">
                            <th class="px-8 py-6 text-emerald-900 font-bold uppercase text-sm tracking-wider">Bulan</th>
                            <th class="px-8 py-6 text-emerald-900 font-bold uppercase text-sm tracking-wider">Jumlah (Rp)
                            </th>
                            <th class="px-8 py-6 text-emerald-900 font-bold uppercase text-sm tracking-wider text-right">
                                Kegiatan / Pilar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        <tr class="hover:bg-emerald-50/30 transition-colors group">
                            <td class="px-8 py-5 text-zinc-700 font-semibold text-lg md:text-xl">Januari</td>
                            <td class="px-8 py-5 text-zinc-600 font-mono text-lg md:text-xl font-medium">80.000.000</td>
                            <td class="px-8 py-5 text-right">
                                <span
                                    class="inline-block px-4 py-1.5 bg-blue-100 text-blue-800 rounded-full text-xs font-bold uppercase tracking-wide group-hover:bg-blue-600 group-hover:text-white transition-all">Pendidikan</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-emerald-50/30 transition-colors group">
                            <td class="px-8 py-5 text-zinc-700 font-semibold text-lg md:text-xl">Februari</td>
                            <td class="px-8 py-5 text-zinc-600 font-mono text-lg md:text-xl font-medium">90.000.000</td>
                            <td class="px-8 py-5 text-right">
                                <span
                                    class="inline-block px-4 py-1.5 bg-red-100 text-red-800 rounded-full text-xs font-bold uppercase tracking-wide group-hover:bg-red-600 group-hover:text-white transition-all">Kesehatan</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-emerald-50/30 transition-colors group">
                            <td class="px-8 py-5 text-zinc-700 font-semibold text-lg md:text-xl">Maret</td>
                            <td class="px-8 py-5 text-zinc-600 font-mono text-lg md:text-xl font-medium">110.000.000</td>
                            <td class="px-8 py-5 text-right">
                                <span
                                    class="inline-block px-4 py-1.5 bg-yellow-100 text-yellow-800 rounded-full text-xs font-bold uppercase tracking-wide group-hover:bg-yellow-600 group-hover:text-white transition-all">Ekonomi</span>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-emerald-800">
                        <tr>
                            <td class="px-8 py-5 text-white font-bold text-xl">Total Pentasarufan</td>
                            <td class="px-8 py-5 text-white font-mono text-2xl font-bold" colspan="2">Rp 280.000.000
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>

    <section id="stats" class="py-12 px-6 max-w-7xl mx-auto space-y-12">
        <div class="mb-10 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-700 rounded-xl flex items-center justify-center shadow-lg shadow-green-100">
                <i class="fas fa-chart-line text-white text-xl"></i>
            </div>
            <h2 class="text-[#014421] text-3xl md:text-4xl font-extrabold tracking-tight">
                Statistik Aktif
            </h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div
                class="bg-white p-8 rounded-3xl shadow-sm border border-zinc-100 hover:shadow-md transition-all duration-300">
                <div class="flex flex-col items-center text-center gap-6">
                    <div class="relative w-40 h-40">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="16" fill="none" class="text-zinc-100"
                                stroke-width="3" stroke="currentColor"></circle>
                            <circle cx="18" cy="18" r="16" fill="none" class="text-green-600"
                                stroke-width="3" stroke-dasharray="75, 100" stroke-linecap="round"
                                stroke="currentColor"></circle>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <h3 class="text-4xl font-black text-[#014421]">150</h3>
                            <span class="text-sm font-bold text-green-700 uppercase tracking-tighter">75% Aktif</span>
                        </div>
                    </div>
                    <div class="text-left w-full space-y-2 border-t border-zinc-50 pt-6">
                        <p class="text-zinc-800 font-bold text-lg flex items-center gap-3">
                            <i class="fa-solid fa-sitemap text-green-700 text-sm"></i>
                            Jumlah Ranting
                        </p>
                        <p class="text-zinc-500 text-sm leading-relaxed text-pretty">
                            Persentase ranting tingkat desa/kelurahan yang menjalankan program kerja aktif.
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white p-8 rounded-3xl shadow-sm border border-zinc-100 hover:shadow-md transition-all duration-300">
                <div class="flex flex-col items-center text-center gap-6">
                    <div class="relative w-40 h-40">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="16" fill="none" class="text-zinc-100"
                                stroke-width="3" stroke="currentColor"></circle>
                            <circle cx="18" cy="18" r="16" fill="none" class="text-emerald-600"
                                stroke-width="3" stroke-dasharray="90, 100" stroke-linecap="round"
                                stroke="currentColor"></circle>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <h3 class="text-4xl font-black text-[#014421]">25</h3>
                            <span class="text-sm font-bold text-emerald-700 uppercase tracking-tighter">90% Aktif</span>
                        </div>
                    </div>
                    <div class="text-left w-full space-y-2 border-t border-zinc-50 pt-6">
                        <p class="text-zinc-800 font-bold text-lg flex items-center gap-3">
                            <i class="fa-solid fa-building-flag text-emerald-700 text-sm"></i>
                            Jumlah MWC
                        </p>
                        <p class="text-zinc-500 text-sm leading-relaxed text-pretty">
                            Persentase pengurus tingkat kecamatan yang berkhidmat dalam pengelolaan ZIS.
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white p-8 rounded-3xl shadow-sm border border-zinc-100 hover:shadow-md transition-all duration-300">
                <h4 class="text-zinc-800 font-bold text-lg mb-6 flex items-center gap-3">
                    <i class="fa-solid fa-chart-pie text-blue-600 text-sm"></i>
                    Rasio Perolehan & Pentasarufan
                </h4>
                <div class="relative flex justify-center">
                    <canvas id="pieChart" class="max-w-[220px] max-h-[220px]"></canvas>
                </div>
                <div class="mt-8 grid grid-cols-2 gap-4 text-center border-t border-zinc-50 pt-6">
                    <div>
                        <span class="block text-2xl font-black text-[#90be6d]">60%</span>
                        <span class="text-xs text-zinc-400 uppercase font-bold tracking-widest">Perolehan</span>
                    </div>
                    <div>
                        <span class="block text-2xl font-black text-[#014421]">40%</span>
                        <span class="text-xs text-zinc-400 uppercase font-bold tracking-widest">Pentasarufan</span>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('pieChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Perolehan', 'Pentasarufan'],
                    datasets: [{
                        data: [60, 40],
                        backgroundColor: ['#90be6d', '#014421'],
                        borderColor: '#ffffff',
                        borderWidth: 4,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        }, // Legend manual dibuat di HTML agar lebih cantik
                        tooltip: {
                            backgroundColor: '#18181b',
                            padding: 12,
                            cornerRadius: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return ` ${context.label}: ${context.parsed}%`;
                                }
                            }
                        }
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true,
                        duration: 2000
                    }
                }
            });
        });
    </script>
@endsection
