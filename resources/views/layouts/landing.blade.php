<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lazisnu - Landing Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans bg-[#f0f2f5] text-gray-800">
    <nav class="sticky top-0 z-50 w-full" x-data="{ isMobileMenuOpen: false }">
        <div class="bg-white/10 px-4 py-3 backdrop-blur-md border-b border-white/10 sm:px-6 lg:px-8">
            <div
                class="relative mx-auto flex max-w-7xl items-center justify-between rounded-2xl border border-zinc-200/50 bg-white/90 p-3 shadow-xl shadow-black/5 backdrop-blur-md">

                <div class="flex w-full items-center justify-between gap-2 md:hidden">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 object-contain">
                    <button @click="isMobileMenuOpen = !isMobileMenuOpen" class="flex h-10 w-10 items-center justify-center rounded-xl bg-zinc-100 text-zinc-600 transition hover:text-green-700 focus:outline-none">
                        <i class="fas" :class="isMobileMenuOpen ? 'fa-times' : 'fa-bars'"></i>
                    </button>
                </div>

                <div class="no-scrollbar hidden items-center gap-1 overflow-x-auto md:flex md:gap-4">
                    <a href="#profile"
                        class="px-4 py-2 text-zinc-600 font-bold text-sm hover:text-[#014421] hover:bg-green-50 rounded-xl transition-all">Profil</a>
                    <a href="#rekap-perolehan"
                        class="px-4 py-2 text-zinc-600 font-bold text-sm hover:text-[#014421] hover:bg-green-50 rounded-xl transition-all">Perolehan</a>
                    <a href="#rekap-pentasarufan"
                        class="px-4 py-2 text-zinc-600 font-bold text-sm hover:text-[#014421] hover:bg-green-50 rounded-xl transition-all">Pentasarufan</a>
                    <a href="#stats"
                        class="px-4 py-2 text-zinc-600 font-bold text-sm hover:text-[#014421] hover:bg-green-50 rounded-xl transition-all">Statistik</a>
                </div>

                <div class="ml-4 hidden items-center md:flex">
                    <a href="{{ route('login') }}"
                        class="bg-[#014421] text-white py-2 px-5 rounded-xl font-bold shadow-lg hover:bg-green-800 transition-all text-sm whitespace-nowrap">
                        Login <i class="fas fa-sign-in-alt ml-2 opacity-70"></i>
                    </a>
                </div>

                <!-- Mobile Menu Dropdown -->
                <div x-show="isMobileMenuOpen" 
                     x-transition:enter="transition ease-out duration-200" 
                     x-transition:enter-start="opacity-0 -translate-y-2" 
                     x-transition:enter-end="opacity-100 translate-y-0" 
                     x-transition:leave="transition ease-in duration-150" 
                     x-transition:leave-start="opacity-100 translate-y-0" 
                     x-transition:leave-end="opacity-0 -translate-y-2" 
                     @click.away="isMobileMenuOpen = false"
                     class="absolute left-0 right-0 top-full mt-2 flex flex-col gap-2 rounded-2xl border border-zinc-200/50 bg-white p-4 shadow-xl md:hidden"
                     style="display: none;" x-cloak>
                    <a @click="isMobileMenuOpen = false" href="#profile" class="px-4 py-3 text-sm font-bold text-zinc-600 transition-all hover:bg-green-50 hover:text-[#014421] rounded-xl">Profil</a>
                    <a @click="isMobileMenuOpen = false" href="#rekap-perolehan" class="px-4 py-3 text-sm font-bold text-zinc-600 transition-all hover:bg-green-50 hover:text-[#014421] rounded-xl">Perolehan</a>
                    <a @click="isMobileMenuOpen = false" href="#rekap-pentasarufan" class="px-4 py-3 text-sm font-bold text-zinc-600 transition-all hover:bg-green-50 hover:text-[#014421] rounded-xl">Pentasarufan</a>
                    <a @click="isMobileMenuOpen = false" href="#stats" class="px-4 py-3 text-sm font-bold text-zinc-600 transition-all hover:bg-green-50 hover:text-[#014421] rounded-xl">Statistik</a>
                    <div class="my-2 border-t border-zinc-100"></div>
                    <a href="{{ route('login') }}" class="rounded-xl bg-[#014421] px-5 py-3 text-center text-sm font-bold text-white shadow-lg transition-all hover:bg-green-800">
                        Login <i class="fas fa-sign-in-alt ml-2 opacity-70"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <style>
        html {
            scroll-behavior: smooth;
        }

        /* Tambahkan offset agar saat scroll, judul section tidak tertutup navbar */
        section {
            scroll-margin-top: 100px;
        }
    </style>

    <style>
        /* Menghilangkan scrollbar tapi tetap bisa scroll di mobile jika menu terlalu panjang */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <main>
        @yield('content')
    </main>
</body>

<footer class="relative bg-gradient-to-b from-[#014421] to-[#002a15] text-white py-20 px-6 mt-20 overflow-hidden">
    <div class="absolute inset-0 opacity-5 pointer-events-none">
        <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
            <rect width="100%" height="100%" fill="url(#grid)" />
        </svg>
    </div>

    <div
        class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-transparent via-green-400 to-transparent opacity-50">
    </div>

    <div class="max-w-7xl mx-auto relative z-10">
        <div class="flex flex-col md:flex-row items-center justify-between gap-16">

                <div class="flex-1 space-y-8 text-center md:text-left">
                <div class="space-y-4">
                    <h3 class="text-green-400 text-sm font-black uppercase tracking-[0.3em]">Hubungi Kami</h3>
                    <a href="https://maps.app.goo.gl/wb9U7uhE2SR8eDZW8" target="_blank" class="block group">
                        <h2 class="text-3xl md:text-4xl font-extrabold group-hover:text-green-400 transition-colors">LAZISNU PC <br>Kabupaten Pekalongan</h2>
                    </a>
                    <div class="w-16 h-1 bg-green-500 rounded-full mx-auto md:mx-0"></div>
                </div>

                <div class="space-y-4">
                    <div class="group flex items-center justify-center md:justify-start gap-4 transition-all">
                        <div
                            class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center group-hover:bg-green-600 transition-colors">
                            <i class="fas fa-phone text-sm"></i>
                        </div>
                        <div class="flex flex-col text-left">
                            <span class="text-xs text-green-400 font-bold uppercase tracking-wider">Telepon</span>
                            <a href="tel:085701049452"
                                class="text-lg font-medium hover:text-green-300 transition-colors">085701049452</a>
                        </div>
                    </div>

                    <div class="group flex items-center justify-center md:justify-start gap-4 transition-all">
                        <div
                            class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center group-hover:bg-green-600 transition-colors">
                            <i class="fas fa-envelope text-sm"></i>
                        </div>
                        <div class="flex flex-col text-left">
                            <span class="text-xs text-green-400 font-bold uppercase tracking-wider">Email</span>
                            <a href="mailto:lazisnupekalongan@gmail.com"
                                class="text-lg font-medium hover:text-green-300 transition-colors">lazisnupekalongan@gmail.com</a>
                        </div>
                    </div>
                </div>

                <div class="pt-10 border-t border-white/10 text-sm opacity-50 italic">
                    <p>© 2026 LAZISNU Kabupaten Pekalongan. <br>Dedikasi untuk Kemaslahatan Umat.</p>
                </div>
            </div>

            <div class="flex-1 w-full max-w-xl">
                <div class="relative group">
                    <div
                        class="absolute -inset-1 bg-gradient-to-r from-green-600 to-emerald-600 rounded-[2.5rem] blur opacity-25 group-hover:opacity-50 transition duration-1000">
                    </div>

                    <div
                        class="relative bg-zinc-900 rounded-[2rem] overflow-hidden border border-white/10 shadow-2xl h-[350px]">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4486.406085288493!2d109.63825999999999!3d-6.960434200000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e70210d13f5ec79%3A0x70f090acf861956a!2sGedung%20PCNU%20Kab.%20Pekalongan!5e1!3m2!1sid!2sid!4v1773977795850!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

</html>
