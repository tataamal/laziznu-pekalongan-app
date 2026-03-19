<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Lazisnu App' }}</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('vite-scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body class="min-h-screen bg-zinc-100 text-zinc-900 antialiased">
    <div class="flex min-h-screen gap-4 p-4">
        <aside class="hidden w-64 shrink-0 rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm lg:flex lg:flex-col">
            <div class="mb-8 flex flex-col items-center gap-3 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-35 object-contain">
            </div>

            <div class="mb-3 px-2 text-[11px] font-semibold uppercase tracking-[0.18em] text-zinc-400">
                Menu
            </div>

            <nav class="flex flex-1 flex-col gap-1">
                @if(auth()->user()->isDeveloper())
                    <a href="{{ route('developer.dashboard') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('developer.dashboard') ? 'bg-green-700 text-white shadow-md' : 'text-zinc-600 hover:bg-zinc-100 hover:text-green-700' }}">
                        <i class="fas fa-home text-sm"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('developer.users.index') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('developer.users.*') ? 'bg-green-700 text-white shadow-md' : 'text-zinc-600 hover:bg-zinc-100 hover:text-green-700' }}">
                        <i class="fas fa-users text-sm"></i>
                        <span>Manajemen User</span>
                    </a>

                    <a href="{{ route('developer.wilayah.index') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('developer.wilayah.*') ? 'bg-green-700 text-white shadow-md' : 'text-zinc-600 hover:bg-zinc-100 hover:text-green-700' }}">
                        <i class="fas fa-map-marker-alt text-sm"></i>
                        <span>Kelola Wilayah</span>
                    </a>

                    <a href="#"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium text-zinc-600 hover:bg-zinc-100 hover:text-green-700">
                        <i class="fas fa-chart-bar text-sm"></i>
                        <span>Laporan</span>
                    </a>
                @elseif(auth()->user()->isRanting())
                    @php
                        $dashboardRoute = auth()->user()->role . '.dashboard';
                    @endphp
                    <a href="{{ Route::has($dashboardRoute) ? route($dashboardRoute) : '#' }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs($dashboardRoute) ? 'bg-green-700 text-white shadow-md' : 'text-zinc-600 hover:bg-zinc-100 hover:text-green-700' }}">
                        <i class="fas fa-home text-sm"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('ranting.income.index') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('ranting.income.*') ? 'bg-green-700 text-white shadow-md' : 'text-zinc-600 hover:bg-zinc-100 hover:text-green-700' }}">
                        <i class="fas fa-wallet text-sm"></i>
                        <span>Input Koin NU</span>
                    </a>

                    <a href="{{ route('ranting.distribution.index') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('ranting.distribution.*') ? 'bg-green-700 text-white shadow-md' : 'text-zinc-600 hover:bg-zinc-100 hover:text-green-700' }}">
                        <i class="fas fa-hand-holding-usd text-sm"></i>
                        <span>Catat Pentasarufan</span>
                    </a>
                    
                    <a href="{{ route('ranting.call-center') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('ranting.call-center') ? 'bg-green-700 text-white shadow-md' : 'text-zinc-600 hover:bg-zinc-100 hover:text-green-700' }}">
                        <i class="fas fa-headset text-sm"></i>
                        <span>Call Center Admin {{ strtoupper(auth()->user()->role) }}</span>
                    </a>
                @elseif(auth()->user()->isMwc())
                    <a href="{{ route('mwc.dashboard') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('mwc.dashboard') ? 'bg-green-700 text-white shadow-md' : 'text-zinc-600 hover:bg-zinc-100 hover:text-green-700' }}">
                        <i class="fas fa-home text-sm"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('mwc.approval-income-koin-nu') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('mwc.approval-income-koin-nu') ? 'bg-green-700 text-white shadow-md' : 'text-zinc-600 hover:bg-zinc-100 hover:text-green-700' }}">
                        <i class="fas fa-clipboard-check text-sm"></i>
                        <span>Persetujuan Input Koin NU</span>
                    </a>

                    <a href="{{ route('mwc.approval-distribution-koin-nu') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('mwc.approval-distribution-koin-nu') ? 'bg-green-700 text-white shadow-md' : 'text-zinc-600 hover:bg-zinc-100 hover:text-green-700' }}">
                        <i class="fas fa-file-signature text-sm"></i>
                        <span>Persetujuan Catat Pentasarufan</span>
                    </a>

                    <a href="{{ route('mwc.infaq-transaction.index') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('mwc.infaq-transaction.*') ? 'bg-green-700 text-white shadow-md' : 'text-zinc-600 hover:bg-zinc-100 hover:text-green-700' }}">
                        <i class="fas fa-hand-holding-heart text-sm"></i>
                        <span>Input Data Infaq</span>
                    </a>
                    
                    <a href="{{ route('mwc.call-center') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('mwc.call-center') ? 'bg-green-700 text-white shadow-md' : 'text-zinc-600 hover:bg-zinc-100 hover:text-green-700' }}">
                        <i class="fas fa-headset text-sm"></i>
                        <span>Call Center Admin {{ strtoupper(auth()->user()->role) }}</span>
                    </a>
                @elseif(auth()->user()->isPc())
                    @php
                        $dashboardRoute = auth()->user()->role . '.dashboard';
                    @endphp
                    <a href="{{ Route::has($dashboardRoute) ? route($dashboardRoute) : '#' }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs($dashboardRoute) ? 'bg-green-700 text-white shadow-md' : 'text-zinc-600 hover:bg-zinc-100 hover:text-green-700' }}">
                        <i class="fas fa-home text-sm"></i>
                        <span>Dashboard</span>
                    </a>

                     <a href="{{ route('pc.infaq.index') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('pc.infaq.*') ? 'bg-green-700 text-white shadow-md' : 'text-zinc-600 hover:bg-zinc-100 hover:text-green-700' }}">
                        <i class="fas fa-hand-holding-heart text-sm"></i>
                        <span>Export Report Pentasarufan</span>
                    </a>

                    <a href="{{ route('pc.infaq.index') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('pc.infaq.*') ? 'bg-green-700 text-white shadow-md' : 'text-zinc-600 hover:bg-zinc-100 hover:text-green-700' }}">
                        <i class="fas fa-hand-holding-heart text-sm"></i>
                        <span>Transaksi Infaq</span>
                    </a>

                    <a href="{{ route('pc.data-transaksi-ranting') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('pc.data-transaksi-ranting') ? 'bg-green-700 text-white shadow-md' : 'text-zinc-600 hover:bg-zinc-100 hover:text-green-700' }}">
                        <i class="fas fa-list-check text-sm"></i>
                        <span>Data Transaksi Per Ranting</span>
                    </a>

                    <a href="{{ route('pc.data-transaksi-mwc') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('pc.data-transaksi-mwc') ? 'bg-green-700 text-white shadow-md' : 'text-zinc-600 hover:bg-zinc-100 hover:text-green-700' }}">
                        <i class="fas fa-file-invoice-dollar text-sm"></i>
                        <span>Data Transaksi Per MWC</span>
                    </a>
                    
                    <a href="{{ route('pc.call-center') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('pc.call-center') ? 'bg-green-700 text-white shadow-md' : 'text-zinc-600 hover:bg-zinc-100 hover:text-green-700' }}">
                        <i class="fas fa-headset text-sm"></i>
                        <span>Call Center Admin {{ strtoupper(auth()->user()->role) }}</span>
                    </a>
                @endif
            </nav>


        </aside>

        <div class="flex-1 rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm lg:p-5">
            <header class="mb-4 flex flex-col gap-4 rounded-3xl border border-zinc-200 bg-zinc-50 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-lg font-semibold tracking-tight">@yield('page_title', 'Dashboard')</h2>
                    <p class="mt-1 text-sm text-zinc-500">
                        @yield('page_subtitle', 'Kelola sistem dengan tampilan yang rapi dan efisien.')
                    </p>
                </div>

                <div class="flex items-center gap-3 self-start lg:self-auto relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false" class="flex items-center gap-3 rounded-2xl border border-zinc-200 bg-white px-3 py-2 text-left transition hover:bg-zinc-50 focus:outline-none">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-lime-400 to-green-700 text-sm font-bold text-white">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="leading-tight">
                            <div class="text-sm font-semibold">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-zinc-500">
                                {{ auth()->user()->role === 'admin_it' ? 'developer' : auth()->user()->role }}
                            </div>
                        </div>
                        <i class="fas fa-chevron-down ml-2 text-xs text-zinc-400 transition-transform duration-200" :class="{'rotate-180': open}"></i>
                    </button>

                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-100" 
                         x-transition:enter-start="transform opacity-0 scale-95" 
                         x-transition:enter-end="transform opacity-100 scale-100" 
                         x-transition:leave="transition ease-in duration-75" 
                         x-transition:leave-start="transform opacity-100 scale-100" 
                         x-transition:leave-end="transform opacity-0 scale-95" 
                         class="absolute right-0 top-full mt-2 w-48 overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-lg z-50" 
                         style="display: none;" x-cloak>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-zinc-700 hover:bg-zinc-50 hover:text-green-700 transition">
                            <i class="fas fa-user-edit w-4"></i>
                            <span>Edit Profile</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-3 px-4 py-3 text-sm font-medium text-zinc-700 hover:bg-red-50 hover:text-red-600 transition text-left">
                                <i class="fas fa-sign-out-alt w-4 transition-colors"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            @yield('content')
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "{{ session('error') }}",
                });
            @endif

            @if($errors->any())
                Toast.fire({
                    icon: 'error',
                    title: 'Terjadi kesalahan validasi',
                    text: "{{ $errors->first() }}"
                });
            @endif

            // Global Delete Confirmation
            window.confirmDelete = function(formId) {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(formId).submit();
                    }
                });
            };
        });
    </script>
</body>
</html>