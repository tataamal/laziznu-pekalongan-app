<x-guest-layout>
    <div class="fixed inset-0 flex items-center justify-center bg-gray-50 font-sans p-4">

        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-black text-[#014421] tracking-tight leading-none">
                    Lazis<span class="text-green-600">NU</span> <span class="block text-2xl mt-1 text-green-600">Kabupaten
                        Pekalongan</span>
                </h1>
                <div class="w-10 h-1 bg-green-500 mx-auto mt-3 rounded-full"></div>
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-400 mt-4 leading-relaxed">
                    Lembaga Amil Zakat, Infaq, dan Shodaqoh <br> Nahdlatul Ulama
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 md:p-10">
                <div class="mb-8 text-center">
                    <h2 class="text-lg font-bold text-gray-800 uppercase tracking-widest">Lupa Password</h2>
                    <p class="text-gray-500 text-xs mt-3 leading-relaxed">
                        Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
                    </p>
                </div>

                <x-auth-session-status class="mb-6" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Email
                            Terdaftar</label>
                        <input id="email" type="email" name="email" :value="old('email')" required autofocus
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-[#014421] focus:bg-white transition-all outline-none text-gray-800 text-sm rounded-lg shadow-sm"
                            placeholder="username@gmail.com">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="space-y-4">
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-[#03a055] to-[#014421] hover:from-[#014421] hover:to-[#002a15] text-white font-black py-4 rounded-lg shadow-lg transition-all duration-300 active:scale-[0.98] flex items-center justify-center gap-3 text-sm uppercase tracking-widest">
                            <span>Kirim Tautan Reset</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-80" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </button>

                        <div class="text-center mt-4">
                            <a href="{{ route('login') }}"
                                class="text-xs font-bold text-gray-400 hover:text-[#014421] transition-colors uppercase tracking-[0.2em]">
                                Kembali ke Login
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <p class="text-center mt-10 text-gray-400 text-[9px] font-bold uppercase tracking-[0.3em]">
                &copy; 2026 LazisNU PC Kabupaten Pekalongan
            </p>
        </div>
    </div>
</x-guest-layout>
