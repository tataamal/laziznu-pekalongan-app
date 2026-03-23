@extends('layouts.app')

@section('page_title', 'Dashboard')
@section('page_subtitle', 'Pantau pengguna, aktivitas, dan statistik sistem.')

@section('content')
    @push('vite-scripts')
        @vite('resources/js/developer-dashboard.js')
    @endpush
    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl bg-gradient-to-br from-green-700 to-emerald-500 p-5 text-white shadow-sm">
            <div class="text-sm font-medium text-white/90">Total User Aktif</div>
            <div class="mt-4 text-4xl font-bold">{{ $totalAllUsers }}</div>
            <div class="mt-3 text-xs text-white/80">Total pengguna aktif</div>
        </div>

        <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
            <div class="text-sm font-medium text-zinc-600">User PC</div>
            <div class="mt-4 text-4xl font-bold tracking-tight text-zinc-900">{{ $totalPc }}</div>
            <div class="mt-3 text-xs text-zinc-500">Jumlah user Pimpinan Cabang NU Pekalongan</div>
        </div>

        <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
            <div class="text-sm font-medium text-zinc-600">User MWC</div>
            <div class="mt-4 text-4xl font-bold tracking-tight text-zinc-900">{{ $totalMwc }}</div>
            <div class="mt-3 text-xs text-zinc-500">Jumlah user MWC NU Pekalongan</div>
        </div>

        <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
            <div class="text-sm font-medium text-zinc-600">User Ranting</div>
            <div class="mt-4 text-4xl font-bold tracking-tight text-zinc-900">{{ $totalRanting }}</div>
            <div class="mt-3 text-xs text-zinc-500">Total akun role Ranting</div>
        </div>
    </section>

    <section class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-3">
        <!-- Aktivitas Terakhir (col-span-1) -->
        <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-zinc-900">Aktivitas Terakhir Anda</h3>
            <div class="mt-4 space-y-4 overflow-y-auto max-h-[320px] pr-2 relative">
                @forelse ($aktivitasUser as $aktivitas)
                    <div class="border-b border-zinc-100 pb-3 last:border-b-0 last:pb-0">
                        <div class="text-sm font-medium text-zinc-800">{{ $aktivitas->description ?? ucfirst($aktivitas->action) }}</div>
                        <div class="mt-1 flex items-center justify-between text-xs text-zinc-500">
                            <span>{{ $aktivitas->created_at->diffForHumans() }}</span>
                            <span class="text-[10px] text-zinc-400">{{ $aktivitas->ip_address }}</span>
                        </div>
                    </div>
                @empty
                    <div class="py-4 text-center text-sm text-zinc-200">
                        Belum ada aktivitas yang tercatat.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Rekapitulasi Perolehan KOIN NU (col-span-2) -->
        <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm xl:col-span-2">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-sm font-semibold text-zinc-900">Rekapitulasi Perolehan KOIN NU</h3>
                <div class="flex flex-wrap gap-2">
                    <select id="filter-status" class="bg-zinc-50 border border-zinc-200 rounded-lg px-2 py-1 text-xs font-semibold text-zinc-700 focus:ring-1 focus:ring-green-500 outline-none w-24">
                        <option value="validated" {{ $status == 'validated' ? 'selected' : '' }}>Validated</option>
                        <option value="on_process" {{ $status == 'on_process' ? 'selected' : '' }}>On Process</option>
                        <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua Status</option>
                    </select>
                    <select id="filter-month" class="bg-zinc-50 border border-zinc-200 rounded-lg px-2 py-1 text-xs font-semibold text-zinc-700 focus:ring-1 focus:ring-green-500 outline-none w-24">
                        @foreach($months as $key => $name)
                            <option value="{{ $key }}" {{ $month == $key ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <select id="filter-year" class="bg-zinc-50 border border-zinc-200 rounded-lg px-2 py-1 text-xs font-semibold text-zinc-700 focus:ring-1 focus:ring-green-500 outline-none w-20">
                        @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto overflow-y-auto max-h-[320px]">
                <table class="w-full border-collapse">
                    <thead class="sticky top-0 z-10 bg-white shadow-sm outline outline-1 outline-zinc-100">
                        <tr class="text-left text-xs uppercase tracking-wide text-zinc-500">
                            <th class="px-3 py-3 font-semibold bg-white">Ranting</th>
                            <th class="px-3 py-3 font-semibold bg-white">Jumlah (Rp)</th>
                            <th class="px-3 py-3 font-semibold text-right bg-white">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="income-table-body" class="divide-y divide-zinc-100">
                        @forelse($incomeData['items'] as $item)
                        <tr class="text-sm text-zinc-700 hover:bg-zinc-50">
                            <td class="px-3 py-3 font-medium">{{ $item['ranting'] }}</td>
                            <td class="px-3 py-3">{{ number_format($item['total'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right">
                                @foreach($item['sources'] as $source)
                                <span class="inline-block px-2 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-semibold">{{ $source }}</span>
                                @endforeach
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-3 py-6 text-center text-sm text-zinc-500">Tidak ada data untuk bulan ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="sticky bottom-0 z-10">
                        <tr class="border-t border-zinc-200 bg-zinc-50 outline outline-1 outline-zinc-200">
                            <td class="px-3 py-3 font-semibold text-zinc-900 text-sm bg-zinc-50">Total Keseluruhan</td>
                            <td id="income-total" class="px-3 py-3 font-bold text-green-700 text-sm bg-zinc-50" colspan="2">Rp {{ number_format($incomeData['total_all'], 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>

    <!-- Row for Pentasarufan and Charts -->
    <section class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
        <!-- Rekapitulasi Pentasarufan KOIN NU -->
        <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-sm font-semibold text-zinc-900">Rekapitulasi Pentasarufan KOIN NU</h3>
            </div>
            <div class="overflow-x-auto overflow-y-auto max-h-[320px]">
                <table class="w-full border-collapse">
                    <thead class="sticky top-0 z-10 bg-white shadow-sm outline outline-1 outline-zinc-100">
                        <tr class="text-left text-xs uppercase tracking-wide text-zinc-500">
                            <th class="px-3 py-3 font-semibold bg-white">Ranting</th>
                            <th class="px-3 py-3 font-semibold bg-white">Jumlah (Rp)</th>
                            <th class="px-3 py-3 font-semibold text-right bg-white">Pilar</th>
                        </tr>
                    </thead>
                    <tbody id="distribution-table-body" class="divide-y divide-zinc-100">
                        @forelse($distributionData['items'] as $item)
                        <tr class="text-sm text-zinc-700 hover:bg-zinc-50">
                            <td class="px-3 py-3 font-medium">{{ $item['ranting'] }}</td>
                            <td class="px-3 py-3">{{ number_format($item['total'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right">
                                @foreach($item['pillars'] as $pillar)
                                <span class="inline-block px-2 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold">{{ $pillar }}</span>
                                @endforeach
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-3 py-6 text-center text-sm text-zinc-500">Tidak ada data untuk bulan ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="sticky bottom-0 z-10">
                        <tr class="border-t border-zinc-200 bg-zinc-50 outline outline-1 outline-zinc-200">
                            <td class="px-3 py-3 font-semibold text-zinc-900 text-sm bg-zinc-50">Total Pentasarufan</td>
                            <td id="distribution-total" class="px-3 py-3 font-bold text-blue-700 text-sm bg-zinc-50" colspan="2">Rp {{ number_format($distributionData['total_all'], 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Infaq Stats Charts Container -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <!-- MWC Chart -->
            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm flex flex-col">
                <div class="flex flex-col gap-2 mb-4">
                    <h3 class="text-sm font-semibold text-zinc-900">Statistik Infaq MWC</h3>
                    <select id="filter-wilayah" class="bg-zinc-50 border border-zinc-200 rounded-lg px-2 py-1 text-xs font-semibold text-zinc-700 focus:ring-1 focus:ring-green-500 outline-none w-full">
                        <option value="all">Semua Wilayah</option>
                        @foreach($wilayahs as $w)
                            <option value="{{ $w->id }}" {{ $wilayahId == $w->id ? 'selected' : '' }}>{{ $w->nama_wilayah }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="relative flex justify-center flex-1 items-center">
                    <canvas id="mwcPieChart" class="max-w-[150px] max-h-[150px]"></canvas>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-2 text-center border-t border-zinc-100 pt-3">
                    <div>
                        <span id="mwc-ratio-income" class="block text-xl font-bold text-green-600">{{ $infaqStats['mwc']['ratio_income'] }}%</span>
                        <span class="text-[10px] text-zinc-500 uppercase font-semibold">Perolehan</span>
                    </div>
                    <div>
                        <span id="mwc-ratio-expense" class="block text-xl font-bold text-blue-600">{{ $infaqStats['mwc']['ratio_expense'] }}%</span>
                        <span class="text-[10px] text-zinc-500 uppercase font-semibold">Pentasarufan</span>
                    </div>
                </div>
            </div>

            <!-- PC Chart -->
            <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm flex flex-col">
                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-zinc-900">Statistik Infaq PC</h3>
                </div>
                <div class="relative flex justify-center flex-1 items-center">
                    <canvas id="pcPieChart" class="max-w-[150px] max-h-[150px]"></canvas>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-2 text-center border-t border-zinc-100 pt-3">
                    <div>
                        <span id="pc-ratio-income" class="block text-xl font-bold text-green-600">{{ $infaqStats['pc']['ratio_income'] }}%</span>
                        <span class="text-[10px] text-zinc-500 uppercase font-semibold">Perolehan</span>
                    </div>
                    <div>
                        <span id="pc-ratio-expense" class="block text-xl font-bold text-blue-600">{{ $infaqStats['pc']['ratio_expense'] }}%</span>
                        <span class="text-[10px] text-zinc-500 uppercase font-semibold">Pentasarufan</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let mwcChart, pcChart;

            function initCharts(mwcData, pcData) {
                const config = (data) => ({
                    type: 'pie',
                    data: {
                        labels: ['Perolehan', 'Pentasarufan'],
                        datasets: [{
                            data: [data.income, data.expense],
                            backgroundColor: ['#16a34a', '#2563eb'], // green-600, blue-600
                            borderColor: '#ffffff',
                            borderWidth: 2,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#18181b',
                                padding: 12,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    label: (context) => ` ${context.label}: ${formatRupiah(context.parsed)}`
                                }
                            }
                        },
                        animation: { animateScale: true, animateRotate: true, duration: 1000 }
                    }
                });

                const mwcCtx = document.getElementById('mwcPieChart').getContext('2d');
                const pcCtx = document.getElementById('pcPieChart').getContext('2d');

                mwcChart = new Chart(mwcCtx, config(mwcData));
                pcChart = new Chart(pcCtx, config(pcData));
            }

            initCharts(@json($infaqStats['mwc']), @json($infaqStats['pc']));

            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(number).replace('Rp', 'Rp ');
            }

            async function updateData() {
                const month = document.getElementById('filter-month').value;
                const year = document.getElementById('filter-year').value;
                const wilayahId = document.getElementById('filter-wilayah').value;
                const status = document.getElementById('filter-status').value;

                // Update Income Table
                try {
                    const incomeRes = await fetch(`/api/stats/income?month=${month}&year=${year}&status=${status}`);
                    const incomeData = await incomeRes.json();
                    const incomeBody = document.getElementById('income-table-body');
                    incomeBody.innerHTML = incomeData.items.length ? incomeData.items.map(item => `
                        <tr class="text-sm text-zinc-700 hover:bg-zinc-50">
                            <td class="px-3 py-3 font-medium">${item.ranting}</td>
                            <td class="px-3 py-3">${new Intl.NumberFormat('id-ID').format(item.total)}</td>
                            <td class="px-3 py-3 text-right">
                                ${item.sources.map(s => `<span class="inline-block px-2 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-semibold">${s}</span>`).join('')}
                            </td>
                        </tr>
                    `).join('') : '<tr><td colspan="3" class="px-3 py-6 text-center text-sm text-zinc-500">Tidak ada data untuk bulan ini</td></tr>';
                    document.getElementById('income-total').innerText = formatRupiah(incomeData.total_all);
                } catch (e) { console.error(e); }

                // Update Distribution Table
                try {
                    const distRes = await fetch(`/api/stats/distribution?month=${month}&year=${year}&status=${status}`);
                    const distData = await distRes.json();
                    const distBody = document.getElementById('distribution-table-body');
                    distBody.innerHTML = distData.items.length ? distData.items.map(item => `
                        <tr class="text-sm text-zinc-700 hover:bg-zinc-50">
                            <td class="px-3 py-3 font-medium">${item.ranting}</td>
                            <td class="px-3 py-3">${new Intl.NumberFormat('id-ID').format(item.total)}</td>
                            <td class="px-3 py-3 text-right">
                                ${item.pillars.map(p => `<span class="inline-block px-2 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold">${p}</span>`).join('')}
                            </td>
                        </tr>
                    `).join('') : '<tr><td colspan="3" class="px-3 py-6 text-center text-sm text-zinc-500">Tidak ada data untuk bulan ini</td></tr>';
                    document.getElementById('distribution-total').innerText = formatRupiah(distData.total_all);
                } catch (e) { console.error(e); }

                // Update Charts
                try {
                    const infaqRes = await fetch(`/api/stats/infaq?month=${month}&year=${year}&wilayah_id=${wilayahId}`);
                    const infaqData = await infaqRes.json();
                    
                    mwcChart.data.datasets[0].data = [infaqData.mwc.income, infaqData.mwc.expense];
                    mwcChart.update();
                    document.getElementById('mwc-ratio-income').innerText = infaqData.mwc.ratio_income + '%';
                    document.getElementById('mwc-ratio-expense').innerText = infaqData.mwc.ratio_expense + '%';

                    pcChart.data.datasets[0].data = [infaqData.pc.income, infaqData.pc.expense];
                    pcChart.update();
                    document.getElementById('pc-ratio-income').innerText = infaqData.pc.ratio_income + '%';
                    document.getElementById('pc-ratio-expense').innerText = infaqData.pc.ratio_expense + '%';
                } catch (e) { console.error(e); }
            }

            document.getElementById('filter-month').addEventListener('change', updateData);
            document.getElementById('filter-year').addEventListener('change', updateData);
            document.getElementById('filter-wilayah').addEventListener('change', updateData);
            document.getElementById('filter-status').addEventListener('change', updateData);
        });
    </script>
@endsection