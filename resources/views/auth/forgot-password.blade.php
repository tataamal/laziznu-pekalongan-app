<x-guest-layout>
    <x-slot name="title">Lupa Password - LazisNU Pekalongan</x-slot>

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

            <div class="bg-white rounded-[2rem] shadow-2xl border border-emerald-50 p-8 md:p-10 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-green-400 to-emerald-700"></div>
                
                <div class="mb-8 text-center">
                    <h2 class="text-xl font-extrabold text-[#014421] tracking-tight">Lupa Password?</h2>
                    <p class="text-zinc-500 text-sm mt-3 font-medium leading-relaxed">
                        Jangan khawatir! Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
                    </p>
                </div>

                <x-auth-session-status class="mb-6 font-semibold text-emerald-700 bg-emerald-50 p-3 rounded-xl border border-emerald-100 text-sm" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div class="space-y-2 group">
                        <label for="email" class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1 group-focus-within:text-emerald-700 transition-colors">
                            Email Terdaftar
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-1 flex items-center pointer-events-none text-zinc-400 group-focus-within:text-emerald-600 transition-colors">
                                <i class="fas fa-envelope text-sm"></i>
                            </div>
                            <input id="email" type="email" name="email" :value="old('email')" required autofocus
                                class="w-full pl-7 pr-4 py-3 bg-white border-b-2 border-zinc-100 focus:border-emerald-700 transition-all outline-none text-zinc-800 placeholder-zinc-300 font-semibold text-base"
                                placeholder="email@contoh.com">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-500 font-bold" />
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full bg-emerald-900 hover:bg-[#002a15] text-white font-black py-4 rounded-2xl shadow-xl shadow-emerald-900/20 transform hover:-translate-y-1 active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-3 text-sm uppercase tracking-widest">
                            <span>Kirim Reset Link</span>
                            <i class="fas fa-paper-plane opacity-50"></i>
                        </button>

                        <div class="text-center mt-8">
                            <a href="{{ route('login') }}"
                                class="text-xs font-black text-zinc-400 hover:text-emerald-700 transition-colors uppercase tracking-[0.2em] flex items-center justify-center gap-2">
                                <i class="fas fa-arrow-left text-[10px]"></i>
                                Kembali ke Login
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <p class="text-center mt-12 text-zinc-400 text-[10px] font-black uppercase tracking-[0.3em]">
                &copy; 2026 PC LazisNU Kab. Pekalongan
            </p>
        </div>
    </div>
</x-guest-layout>
