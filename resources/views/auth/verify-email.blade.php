<x-guest-layout>
    <x-slot name="title">Verifikasi Email - LazisNU Pekalongan</x-slot>

    <div class="min-h-screen flex items-center justify-center bg-[#f0f4f2] relative overflow-hidden font-sans p-4">
        <!-- Background Grid Decoration -->
        <div class="absolute inset-0 z-0 opacity-[0.05] pointer-events-none">
            <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid" width="50" height="50" patternUnits="userSpaceOnUse">
                        <path d="M 50 0 L 0 0 0 50" fill="none" stroke="#014421" stroke-width="1" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>
        </div>

        <div class="relative z-10 w-full max-w-md">
            <div class="text-center mb-10">
                <div class="inline-block mb-6">
                    <img src="{{ asset('images/logo.png') }}" class="w-28 mx-auto drop-shadow-sm" alt="Logo LazisNU">
                </div>
                <h1 class="text-3xl font-black text-[#014421] tracking-tight leading-none uppercase">
                    Lazis<span class="text-green-600">NU</span>
                </h1>
                <div class="w-12 h-1 bg-green-500 mx-auto mt-4 rounded-full shadow-lg shadow-green-500/20"></div>
            </div>

            <div class="bg-white rounded-[2rem] shadow-2xl border border-emerald-50 p-8 md:p-10 relative overflow-hidden text-center">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-green-400 to-emerald-700"></div>
                
                <div class="w-20 h-20 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 border border-emerald-100 shadow-inner">
                    <i class="fas fa-envelope-open-text text-3xl"></i>
                </div>

                <h2 class="text-2xl font-black text-[#014421] mb-4">Verifikasi Email</h2>
                
                <p class="text-zinc-500 text-sm font-medium leading-relaxed mb-8">
                    Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengeklik tautan yang baru saja kami kirimkan.
                </p>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl text-xs font-bold leading-tight">
                        Tautan verifikasi baru telah dikirimkan ke alamat email yang Anda berikan saat pendaftaran.
                    </div>
                @endif

                <div class="space-y-4">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit"
                            class="w-full bg-emerald-900 hover:bg-[#002a15] text-white font-black py-4 rounded-2xl shadow-xl shadow-emerald-900/20 transform hover:-translate-y-1 active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-3 text-sm uppercase tracking-widest">
                            <span>Kirim Ulang Email</span>
                            <i class="fas fa-paper-plane opacity-50"></i>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-xs font-black text-zinc-400 hover:text-red-600 transition-colors uppercase tracking-[0.2em] flex items-center justify-center gap-2 mx-auto">
                            <i class="fas fa-sign-out-alt text-[10px]"></i>
                            Keluar / Logout
                        </button>
                    </form>
                </div>
            </div>

            <p class="text-center mt-12 text-zinc-400 text-[10px] font-black uppercase tracking-[0.3em]">
                &copy; 2026 PC LazisNU Kab. Pekalongan
            </p>
        </div>
    </div>
</x-guest-layout>
