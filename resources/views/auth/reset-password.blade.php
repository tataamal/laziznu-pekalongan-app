<x-guest-layout>
    <x-slot name="title">Reset Password - LazisNU Pekalongan</x-slot>

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
                    <h2 class="text-xl font-extrabold text-[#014421] tracking-tight">Atur Ulang Password</h2>
                    <p class="text-zinc-500 text-sm mt-3 font-medium leading-relaxed">
                        Silakan buat password baru yang kuat untuk mengamankan akun Anda.
                    </p>
                </div>

                <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address -->
                    <div class="space-y-1.5 group">
                        <label for="email" class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1 group-focus-within:text-emerald-700 transition-colors">Alamat Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-1 flex items-center pointer-events-none text-zinc-400 group-focus-within:text-emerald-600 transition-colors">
                                <i class="fas fa-envelope text-sm"></i>
                            </div>
                            <input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus
                                class="w-full pl-7 pr-4 py-2.5 bg-white border-b-2 border-zinc-100 focus:border-emerald-700 transition-all outline-none text-zinc-800 placeholder-zinc-300 font-semibold text-base"
                                placeholder="email@contoh.com">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-500 font-bold" />
                    </div>

                    <!-- Password -->
                    <div class="space-y-1.5 group" x-data="{ show: false }">
                        <label for="password" class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1 group-focus-within:text-emerald-700 transition-colors">Password Baru</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-1 flex items-center pointer-events-none text-zinc-400 group-focus-within:text-emerald-600 transition-colors">
                                <i class="fas fa-lock text-sm"></i>
                            </div>
                            <input id="password" :type="show ? 'text' : 'password'" name="password" required
                                class="w-full pl-7 pr-10 py-2.5 bg-white border-b-2 border-zinc-100 focus:border-emerald-700 transition-all outline-none text-zinc-800 placeholder-zinc-300 font-semibold text-base"
                                placeholder="••••••••">
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-zinc-400 hover:text-emerald-600 transition-colors">
                                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-500 font-bold" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="space-y-1.5 group" x-data="{ show: false }">
                        <label for="password_confirmation" class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1 group-focus-within:text-emerald-700 transition-colors">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-1 flex items-center pointer-events-none text-zinc-400 group-focus-within:text-emerald-600 transition-colors">
                                <i class="fas fa-check-double text-sm"></i>
                            </div>
                            <input id="password_confirmation" :type="show ? 'text' : 'password'" name="password_confirmation" required
                                class="w-full pl-7 pr-10 py-2.5 bg-white border-b-2 border-zinc-100 focus:border-emerald-700 transition-all outline-none text-zinc-800 placeholder-zinc-300 font-semibold text-base"
                                placeholder="••••••••">
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-zinc-400 hover:text-emerald-600 transition-colors">
                                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs text-red-500 font-bold" />
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            class="w-full bg-emerald-900 hover:bg-[#002a15] text-white font-black py-4 rounded-2xl shadow-xl shadow-emerald-900/20 transform hover:-translate-y-1 active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-3 text-sm uppercase tracking-widest">
                            <span>Reset Password</span>
                            <i class="fas fa-key opacity-50"></i>
                        </button>
                    </div>
                </form>
            </div>

            <p class="text-center mt-12 text-zinc-400 text-[10px] font-black uppercase tracking-[0.3em]">
                &copy; 2026 PC LazisNU Kab. Pekalongan
            </p>
        </div>
    </div>
</x-guest-layout>
