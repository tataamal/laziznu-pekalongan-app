<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-[#f8fafc] relative overflow-hidden font-sans">

        <div class="absolute inset-0 z-0 opacity-[0.03] pointer-events-none">
            <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid" width="50" height="50" patternUnits="userSpaceOnUse">
                        <path d="M 50 0 L 0 0 0 50" fill="none" stroke="#014421" stroke-width="1" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>
        </div>

        <div
            class="relative z-10 w-full max-w-[1100px] flex flex-col md:flex-row bg-white rounded-[3rem] shadow-2xl overflow-hidden m-4 border border-zinc-200/50">

            <div class="hidden md:flex md:w-1/2 bg-[#014421] p-12 flex-col justify-between relative overflow-hidden">
                <div class="absolute -top-20 -left-20 w-64 h-64 bg-green-500/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-emerald-400/10 rounded-full blur-3xl"></div>

                <div class="relative z-10">
                    <div class="w-16 h-1 bg-green-400 mb-6 rounded-full"></div>
                    <h1 class="text-4xl lg:text-5xl font-black text-white leading-tight">
                        Sistem Informasi <br> <span class="text-green-400">LazisNU</span>
                    </h1>
                    <p class="text-green-100/70 mt-6 text-lg leading-relaxed max-w-sm">
                        Kelola data pentasarufan dan perolehan zakat dengan lebih efisien dan transparan.
                    </p>
                </div>

                <div class="relative z-10 flex items-center gap-4 text-white/50 text-sm">
                    <span>LazisNU Kabupaten Pekalongan</span>
                    <span class="w-1 h-1 bg-white/30 rounded-full"></span>
                    <span>v2.0</span>
                </div>
            </div>

            <div class="w-full md:w-1/2 p-8 md:p-16 lg:p-20 bg-white">
                <div class="max-w-sm mx-auto">
                    <div class="mb-10 text-center md:text-left">
                        <h2 class="text-3xl font-black text-zinc-900 tracking-tight">Login Admin</h2>
                        <p class="text-zinc-500 mt-2 font-medium">Silakan masukkan kredensial Anda</p>
                    </div>

                    @if (session('status'))
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1">Email
                                Address</label>
                            <div class="relative group">
                                <input id="email" type="email" name="email" :value="old('email')" required
                                    autofocus
                                    class="w-full pl-0 pr-4 py-3 bg-transparent border-b-2 border-zinc-200 focus:border-[#014421] transition-all outline-none text-zinc-800 placeholder-zinc-300 font-medium text-lg"
                                    placeholder="admin@lazisnu.org">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-500" />
                        </div>

                        <div class="space-y-2">
                            <div class="flex justify-between items-center ml-1">
                                <label
                                    class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-400">Password</label>
                            </div>
                            <input id="password" type="password" name="password" required
                                autocomplete="current-password"
                                class="w-full pl-0 pr-4 py-3 bg-transparent border-b-2 border-zinc-200 focus:border-[#014421] transition-all outline-none text-zinc-800 placeholder-zinc-300 font-medium text-lg"
                                placeholder="••••••••">
                            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-500" />
                        </div>

                        <div class="flex items-center justify-between py-2">
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" name="remember"
                                    class="w-4 h-4 rounded border-zinc-300 text-[#014421] focus:ring-[#014421]">
                                <span
                                    class="ms-2 text-sm font-bold text-zinc-500 group-hover:text-[#014421] transition-colors">Ingat
                                    Saya</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a class="text-sm font-bold text-[#014421] hover:underline"
                                    href="{{ route('password.request') }}">
                                    Lupa Password?
                                </a>
                            @endif
                        </div>

                        <button type="submit"
                            class="w-full bg-[#014421] hover:bg-[#002a15] text-white font-bold py-4 rounded-2xl shadow-xl shadow-green-900/20 transform hover:-translate-y-1 active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-3 mt-4">
                            <span>Masuk ke Dashboard</span>
                            <i class="fas fa-sign-in-alt opacity-50"></i>
                        </button>
                    </form>

                    <p class="mt-10 text-center text-zinc-400 text-xs font-medium uppercase tracking-widest">
                        LazisNU PC Kabupaten Pekalongan
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
