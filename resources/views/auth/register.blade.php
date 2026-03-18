<x-guest-layout>
    <x-slot name="title">Daftar Akun - LazisNU Pekalongan</x-slot>

    <div class="min-h-screen flex items-center justify-center bg-[#f0f4f2] relative overflow-hidden font-sans p-4 md:p-6">
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

        <div class="relative z-10 w-full max-w-[1100px] flex flex-col md:flex-row bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-emerald-100">
            <!-- Left Side: Branding -->
            <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-[#014421] via-[#015e2e] to-[#014421] p-12 lg:p-16 flex-col justify-between relative overflow-hidden text-white">
                <div class="absolute -top-24 -left-24 w-80 h-80 bg-emerald-400/20 rounded-full blur-3xl text-white"></div>
                <div class="absolute -bottom-24 -right-24 w-80 h-80 bg-green-500/10 rounded-full blur-3xl text-white"></div>

                <div class="relative z-10">
                    <div class="mb-10 block">
                        <img src="{{ asset('images/logo.png') }}" class="w-32 brightness-0 invert" alt="Logo LazisNU">
                    </div>
                    <div class="w-20 h-1.5 bg-green-400 mb-8 rounded-full shadow-lg shadow-green-400/20"></div>
                    <h1 class="text-4xl lg:text-5xl font-black leading-tight tracking-tight">
                        Bergabung Menjadi <br> <span class="text-green-300">Pejuang Kebaikan</span>
                    </h1>
                    <p class="text-green-50/80 mt-8 text-lg leading-relaxed max-w-sm font-medium">
                        Mari berkontribusi dalam pengelolaan Zakat, Infaq, dan Shodaqoh yang lebih terorganisir untuk kebermanfaatan umat.
                    </p>
                </div>

                <div class="relative z-10 flex items-center gap-4 text-white/40 text-sm font-bold tracking-widest uppercase">
                    <span>Khidmat untuk Umat</span>
                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span>
                    <span>v2.0</span>
                </div>
            </div>

            <!-- Right Side: Register Form -->
            <div class="w-full md:w-1/2 p-8 md:p-12 lg:p-16 bg-white overflow-y-auto max-h-[90vh]">
                <div class="max-w-sm mx-auto">
                    <!-- Mobile Logo -->
                    <div class="md:hidden flex justify-center mb-8">
                        <img src="{{ asset('images/logo.png') }}" class="w-32" alt="Logo LazisNU">
                    </div>

                    <div class="mb-8 text-center md:text-left">
                        <h2 class="text-3xl font-extrabold text-[#014421] tracking-tight">Daftar Akun</h2>
                        <p class="text-zinc-500 mt-2 font-medium">Silakan lengkapi data diri Anda untuk mendaftar.</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}" class="space-y-5">
                        @csrf

                        <!-- Name -->
                        <div class="space-y-1.5 group">
                            <label for="name" class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1 group-focus-within:text-emerald-700 transition-colors">Nama Lengkap</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-1 flex items-center pointer-events-none text-zinc-400 group-focus-within:text-emerald-600 transition-colors">
                                    <i class="fas fa-user text-sm"></i>
                                </div>
                                <input id="name" type="text" name="name" :value="old('name')" required autofocus
                                    class="w-full pl-7 pr-4 py-2.5 bg-white border-b-2 border-zinc-100 focus:border-emerald-700 transition-all outline-none text-zinc-800 placeholder-zinc-300 font-semibold text-base"
                                    placeholder="Masukkan nama lengkap">
                            </div>
                            <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs text-red-500 font-bold" />
                        </div>

                        <!-- Email Address -->
                        <div class="space-y-1.5 group">
                            <label for="email" class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1 group-focus-within:text-emerald-700 transition-colors">Alamat Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-1 flex items-center pointer-events-none text-zinc-400 group-focus-within:text-emerald-600 transition-colors">
                                    <i class="fas fa-envelope text-sm"></i>
                                </div>
                                <input id="email" type="email" name="email" :value="old('email')" required
                                    class="w-full pl-7 pr-4 py-2.5 bg-white border-b-2 border-zinc-100 focus:border-emerald-700 transition-all outline-none text-zinc-800 placeholder-zinc-300 font-semibold text-base"
                                    placeholder="email@contoh.com">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-500 font-bold" />
                        </div>

                        <!-- Password -->
                        <div class="space-y-1.5 group" x-data="{ show: false }">
                            <label for="password" class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1 group-focus-within:text-emerald-700 transition-colors">Password</label>
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
                            <label for="password_confirmation" class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1 group-focus-within:text-emerald-700 transition-colors">Konfirmasi Password</label>
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
                                <span>Daftar Sekarang</span>
                                <i class="fas fa-user-plus opacity-50"></i>
                            </button>
                        </div>

                        <div class="text-center mt-6">
                            <p class="text-sm font-bold text-zinc-500">
                                Sudah punya akun? 
                                <a class="text-emerald-700 hover:text-emerald-900 hover:underline transition-colors" href="{{ route('login') }}">
                                    Login di sini
                                </a>
                            </p>
                        </div>
                    </form>

                    <div class="mt-10 text-center">
                        <p class="text-zinc-400 text-[9px] font-black uppercase tracking-[0.2em]">
                            PC LazisNU Kabupaten Pekalongan
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
