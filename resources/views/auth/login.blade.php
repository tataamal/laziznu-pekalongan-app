<x-guest-layout>
    <x-slot name="title">Login - LazisNU Pekalongan</x-slot>

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
                    <div class="w-20 h-1.5 bg-green-400 mb-8 rounded-full shadow-lg shadow-green-400/20"></div>
                    <h1 class="text-4xl lg:text-5xl font-black leading-tight tracking-tight">
                        LAZISNU  <br> <span class="text-green-300">PCNU KOTA PEKALONGAN</span>
                    </h1>
                    <p class="text-green-50/80 mt-8 text-lg leading-relaxed max-w-sm font-medium">
                        Lembaga Amil Zakat, Infaq, dan Shodaqoh Nahdlatul Ulama Kota Pekalongan.
                    </p>
                </div>
            </div>

            <!-- Right Side: Login Form -->
            <div class="w-full md:w-1/2 p-8 md:p-14 lg:p-20 bg-white">
                <div class="max-w-sm mx-auto">
                    <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 text-zinc-400 hover:text-emerald-700 transition-colors mb-8 group">
                        <i class="fas fa-arrow-left text-sm group-hover:-translate-x-1 transition-transform"></i>
                        <span class="text-sm font-bold uppercase tracking-widest">Kembali</span>
                    </a>
                    <div class="mb-10 text-center md:text-left">
                        <h2 class="text-3xl font-extrabold text-[#014421] tracking-tight">Login</h2>
                        <p class="text-zinc-500 mt-2 font-medium">Selamat datang kembali! Silakan masuk ke akun Anda.</p>
                    </div>

                    @if (session('status'))
                        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl text-sm font-medium">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Email Address -->
                        <div class="space-y-2 group">
                            <label for="email" class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1 group-focus-within:text-emerald-700 transition-colors">
                                Email Address
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-1 flex items-center pointer-events-none text-zinc-400 group-focus-within:text-emerald-600 transition-colors">
                                    <i class="fas fa-envelope text-sm"></i>
                                </div>
                                <input id="email" type="email" name="email" :value="old('email')" required autofocus
                                    class="w-full pl-7 pr-4 py-3 bg-white border-b-2 border-zinc-100 focus:border-emerald-700 transition-all outline-none text-zinc-800 placeholder-zinc-300 font-semibold text-lg"
                                    placeholder="admin@lazisnu.org">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-500 font-bold" />
                        </div>

                        <!-- Password -->
                        <div class="space-y-2 group" x-data="{ show: false }">
                            <div class="flex justify-between items-center ml-1">
                                <label for="password" class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-400 group-focus-within:text-emerald-700 transition-colors">
                                    Password
                                </label>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-1 flex items-center pointer-events-none text-zinc-400 group-focus-within:text-emerald-600 transition-colors">
                                    <i class="fas fa-lock text-sm"></i>
                                </div>
                                <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                                    class="w-full pl-7 pr-10 py-3 bg-white border-b-2 border-zinc-100 focus:border-emerald-700 transition-all outline-none text-zinc-800 placeholder-zinc-300 font-semibold text-lg"
                                    placeholder="••••••••">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-zinc-400 hover:text-emerald-600 transition-colors">
                                    <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-500 font-bold" />
                        </div>

                        <button type="submit"
                            class="w-full bg-emerald-900 hover:bg-[#002a15] text-white font-black py-4 rounded-2xl shadow-xl shadow-emerald-900/20 transform hover:-translate-y-1 active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-3 text-sm uppercase tracking-widest mt-4">
                            <span>Masuk ke Dashboard</span>
                            <i class="fas fa-arrow-right opacity-50"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
