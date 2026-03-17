<x-guest-layout>
    <div class="fixed inset-0 flex items-center justify-center bg-gray-50 font-sans p-4">

        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-black text-[#014421] tracking-tight leading-none">
                    LazisNU <span class="block text-2xl mt-1 text-green-600">Kabupaten Pekalongan</span>
                </h1>
                <div class="w-10 h-1 bg-green-500 mx-auto mt-3 rounded-full"></div>
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-400 mt-4 leading-relaxed">
                    Lembaga Amil Zakat, Infaq, dan Shodaqoh <br> Nahdlatul Ulama
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 md:p-10">
                <div class="mb-8 text-center">
                    <h2 class="text-lg font-bold text-gray-800 uppercase tracking-widest">Login Akun</h2>
                </div>

                <x-auth-session-status class="mb-6" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label
                            class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Email</label>
                        <input id="email" type="email" name="email" :value="old('email')" required autofocus
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-[#014421] focus:bg-white transition-all outline-none text-gray-800 text-sm rounded-lg shadow-sm"
                            placeholder="esername@gmail.com">
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div>
                        <label
                            class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Password</label>
                        <input id="password" type="password" name="password" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-[#014421] focus:bg-white transition-all outline-none text-gray-800 text-sm rounded-lg shadow-sm"
                            placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember"
                                class="rounded border-gray-300 text-[#014421] focus:ring-[#014421]">
                            <span class="ms-2 text-xs text-gray-500 font-medium">Ingat saya</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a class="text-xs font-bold text-[#014421] hover:underline"
                                href="{{ route('password.request') }}">
                                Lupa Password?
                            </a>
                        @endif
                    </div>

                    <button type="submit"
                        class="w-full bg-gradient-to-r from-[#03a055] to-[#014421] hover:from-[#014421] hover:to-[#002a15] text-white font-black py-4 rounded-lg shadow-lg transition-all duration-300 active:scale-[0.98] flex items-center justify-center gap-3 text-sm uppercase tracking-widest mt-4">
                        <span>Login</span>
                    </button>
                </form>
            </div>

            <p class="text-center mt-10 text-gray-400 text-[9px] font-bold uppercase tracking-[0.3em]">
                &copy; 2026 LazisNU PC Kabupaten Pekalongan
            </p>
        </div>
    </div>
</x-guest-layout>
