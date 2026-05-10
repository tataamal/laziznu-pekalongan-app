import ApexCharts from "apexcharts";

document.addEventListener("DOMContentLoaded", function () {
    const chartDataElement = document.getElementById("mwc-chart-data");

    if (!chartDataElement) return;

    const chartData = JSON.parse(chartDataElement.textContent);

    const palette = {
        primary: "#15803d", // green-700
        primaryDark: "#166534", // green-800
        secondary: "#f59e0b", // amber-500
        soft: "#86efac", // green-300
        soft2: "#fcd34d", // amber-300
        white: "#ffffff",
    };

    // 1. Line Chart: Trend Transaksi (4 Series)
    const incomeLineOptions = {
        series: [
            {
                name: "Pemasukan Koin NU MWC",
                data: chartData.line.income_koin,
            },
            {
                name: "Pentasarufan KOIN NU MWC",
                data: chartData.line.expense_koin,
            },
            {
                name: "Pemasukan Infaq MWC",
                data: chartData.line.income_infaq,
            },
            {
                name: "Pengeluaran Infaq MWC",
                data: chartData.line.expense_infaq,
            },
        ],
        chart: {
            height: 300,
            type: "area",
            fontFamily: "inherit",
            toolbar: { show: false },
        },
        colors: [palette.primary, "#ef4444", palette.secondary, "#ea580c"], // Green, Red, Amber, Orange
        fill: {
            type: "gradient",
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [0, 90, 100],
            },
        },
        dataLabels: { enabled: false },
        stroke: {
            curve: "smooth",
            width: 3,
        },
        xaxis: {
            categories: chartData.line.labels,
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return new Intl.NumberFormat("id-ID", {
                        notation: "compact",
                        compactDisplay: "short",
                        maximumFractionDigits: 1,
                    }).format(val);
                },
            },
        },
        tooltip: {
            shared: true,
            intersect: false,
            custom: function({ series, seriesIndex, dataPointIndex, w }) {
                const month = chartData.line.labels[dataPointIndex];
                
                const incomeKoin = chartData.line.income_koin[dataPointIndex];
                const expenseKoin = chartData.line.expense_koin[dataPointIndex];
                const incomeInfaq = chartData.line.income_infaq[dataPointIndex];
                const expenseInfaq = chartData.line.expense_infaq[dataPointIndex];
                
                let html = `<div class="p-3 bg-white shadow-lg rounded-xl border border-zinc-200 text-xs w-64">
                    <div class="font-semibold text-zinc-800 mb-3 text-sm">${month}</div>`;
                
                // Pemasukan Koin NU
                html += `<div class="mb-3 pb-3 border-b border-zinc-100">
                    <div class="flex justify-between items-center mb-1">
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-[#15803d]"></span>Pemasukan Koin NU</span>
                        <span class="font-bold text-zinc-900">Rp ${new Intl.NumberFormat('id-ID').format(incomeKoin)}</span>
                    </div>`;
                
                // Breakdown Pemasukan Koin NU
                const incomeBreakdown = chartData.line.income_koin_breakdown[dataPointIndex];
                if (incomeBreakdown && Object.keys(incomeBreakdown).length > 0) {
                    html += '<div class="pl-3 text-zinc-500 text-[10px] mt-1 space-y-0.5">';
                    for (const [ranting, val] of Object.entries(incomeBreakdown)) {
                        const label = ranting === 'Transaksi MWC' ? ranting : `Ranting ${ranting}`;
                        html += `<div class="flex justify-between"><span>${label}</span><span>Rp ${new Intl.NumberFormat('id-ID').format(val)}</span></div>`;
                    }
                    html += '</div>';
                }
                html += `</div>`;
                
                // Pentasarufan Koin NU
                html += `<div class="mb-3 pb-3 border-b border-zinc-100">
                    <div class="flex justify-between items-center mb-1">
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-[#ef4444]"></span>Pentasarufan KOIN NU</span>
                        <span class="font-bold text-zinc-900">Rp ${new Intl.NumberFormat('id-ID').format(expenseKoin)}</span>
                    </div>`;
                
                // Breakdown Pentasarufan Koin NU
                const expenseBreakdown = chartData.line.expense_koin_breakdown[dataPointIndex];
                if (expenseBreakdown && Object.keys(expenseBreakdown).length > 0) {
                    html += '<div class="pl-3 text-zinc-500 text-[10px] mt-1 space-y-0.5">';
                    for (const [ranting, val] of Object.entries(expenseBreakdown)) {
                        const label = ranting === 'Transaksi MWC' ? ranting : `Ranting ${ranting}`;
                        html += `<div class="flex justify-between"><span>${label}</span><span>Rp ${new Intl.NumberFormat('id-ID').format(val)}</span></div>`;
                    }
                    html += '</div>';
                }
                html += `</div>`;
                
                // Pemasukan Infaq
                html += `<div class="mb-3 pb-3 border-b border-zinc-100">
                    <div class="flex justify-between items-center">
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-[#f59e0b]"></span>Pemasukan Infaq MWC</span>
                        <span class="font-bold text-zinc-900">Rp ${new Intl.NumberFormat('id-ID').format(incomeInfaq)}</span>
                    </div>
                </div>`;
                
                // Pengeluaran Infaq
                html += `<div class="flex justify-between items-center">
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-[#ea580c]"></span>Pengeluaran Infaq MWC</span>
                    <span class="font-bold text-zinc-900">Rp ${new Intl.NumberFormat('id-ID').format(expenseInfaq)}</span>
                </div>`;
                
                html += `</div>`;
                return html;
            }
        },
    };

    if (document.querySelector("#lineChartIncome")) {
        new ApexCharts(
            document.querySelector("#lineChartIncome"),
            incomeLineOptions,
        ).render();
    }

    // 2. Donut Chart (Distribusi Pentasarufan - Toggleable)
    let donutChart = null;
    
    // Check if data is empty
    const isKoinEmpty = !chartData.pie.koin.data || chartData.pie.koin.data.length === 0 || chartData.pie.koin.data.reduce((a, b) => a + b, 0) === 0;
    
    const donutOptions = {
        series: isKoinEmpty ? [1] : chartData.pie.koin.data,
        labels: isKoinEmpty ? ["Belum ada data"] : chartData.pie.koin.labels,
        chart: {
            type: "donut",
            height: 300,
            fontFamily: "inherit",
        },
        colors: isKoinEmpty ? ["#e4e4e7"] : [
            palette.primary,
            palette.secondary,
            palette.primaryDark,
            palette.soft2,
            "#22c55e",
            "#f87171",
        ],
        plotOptions: {
            pie: {
                donut: {
                    size: "65%",
                    labels: {
                        show: true,
                        name: { show: true, fontSize: "14px", fontWeight: 600 },
                        value: {
                            show: true,
                            fontSize: "16px",
                            fontWeight: 700,
                            formatter: function (val) {
                                if (isKoinEmpty) return "0";
                                return new Intl.NumberFormat("id-ID", {
                                    notation: "compact",
                                    compactDisplay: "short",
                                }).format(val);
                            },
                        },
                        total: {
                            show: true,
                            showAlways: true,
                            label: "Total Koin",
                            fontSize: "14px",
                            formatter: function (w) {
                                if (isKoinEmpty) return "0";
                                const total = w.globals.seriesTotals.reduce(
                                    (a, b) => a + b,
                                    0,
                                );
                                return new Intl.NumberFormat("id-ID", {
                                    notation: "compact",
                                    compactDisplay: "short",
                                }).format(total);
                            },
                        },
                    },
                },
            },
        },
        dataLabels: { enabled: false },
        stroke: { show: false },
        legend: {
            position: "bottom",
            offsetY: 0,
            markers: { radius: 12 },
            show: !isKoinEmpty, // Hide legend if empty
        },
        tooltip: {
            enabled: !isKoinEmpty, // Disable tooltip if empty
            y: {
                formatter: function (val) {
                    return new Intl.NumberFormat("id-ID", {
                        style: "currency",
                        currency: "IDR",
                        maximumFractionDigits: 0,
                    }).format(val);
                },
            },
        },
    };

    if (document.querySelector("#donutChartDistribution")) {
        donutChart = new ApexCharts(
            document.querySelector("#donutChartDistribution"),
            donutOptions,
        );
        donutChart.render();
    }

    // Dropdown filter listener for Donut Chart
    const filterDonut = document.getElementById("filterDonut");
    if (filterDonut) {
        filterDonut.addEventListener("change", function() {
            const val = this.value;
            const data = chartData.pie[val];
            if (data) {
                const isEmpty = !data.data || data.data.length === 0 || data.data.reduce((a, b) => a + b, 0) === 0;
                
                donutChart.updateOptions({
                    series: isEmpty ? [1] : data.data,
                    labels: isEmpty ? ["Belum ada data"] : data.labels,
                    colors: isEmpty ? ["#e4e4e7"] : [
                        palette.primary,
                        palette.secondary,
                        palette.primaryDark,
                        palette.soft2,
                        "#22c55e",
                        "#f87171",
                    ],
                    legend: {
                        show: !isEmpty
                    },
                    tooltip: {
                        enabled: !isEmpty
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                labels: {
                                    value: {
                                        formatter: function(val) {
                                            if (isEmpty) return "0";
                                            return new Intl.NumberFormat("id-ID", {
                                                notation: "compact",
                                                compactDisplay: "short",
                                            }).format(val);
                                        }
                                    },
                                    total: {
                                        label: val === 'koin' ? 'Total Koin' : 'Total Infaq',
                                        formatter: function(w) {
                                            if (isEmpty) return "0";
                                            const total = w.globals.seriesTotals.reduce(
                                                (a, b) => a + b,
                                                0,
                                            );
                                            return new Intl.NumberFormat("id-ID", {
                                                notation: "compact",
                                                compactDisplay: "short",
                                            }).format(total);
                                        }
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    }


    // Table Multi-Filter Data (Preserved)
    const searchInput = document.getElementById("search");
    const filterJenis = document.getElementById("filterJenis");
    const filterRole = document.getElementById("filterRole");
    const filterTanggal = document.getElementById("filterTanggal");
    const tableRows = document.querySelectorAll("#dataTable tbody tr.trx-row");
    const emptyStateRow = document.querySelector(
        "#dataTable tbody tr:not(.trx-row)",
    );

    let flatpickrInstance = null;
    if (filterTanggal) {
        flatpickrInstance = flatpickr(filterTanggal, {
            mode: "range",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d M Y",
            onChange: function () {
                filterTable();
            },
        });
    }

    function filterTable() {
        if (!tableRows.length) return;

        const searchText = searchInput ? searchInput.value.toLowerCase() : "";
        const jenisValue = filterJenis ? filterJenis.value.toLowerCase() : "";
        const roleValue = filterRole ? filterRole.value.toLowerCase() : "";

        let startDate = null;
        let endDate = null;
        if (flatpickrInstance && flatpickrInstance.selectedDates.length > 0) {
            startDate = flatpickrInstance.selectedDates[0];
            startDate.setHours(0, 0, 0, 0);
            if (flatpickrInstance.selectedDates.length > 1) {
                endDate = flatpickrInstance.selectedDates[1];
                endDate.setHours(23, 59, 59, 999);
            } else {
                endDate = new Date(startDate);
                endDate.setHours(23, 59, 59, 999);
            }
        }

        let visibleCount = 0;

        tableRows.forEach((row) => {
            const rowText = row.textContent.toLowerCase();
            const rowJenis = row.getAttribute("data-jenis");
            const rowRole = row.getAttribute("data-role");
            const rowTanggalStr = row.getAttribute("data-tanggal");
            const rowTanggal = new Date(rowTanggalStr);
            rowTanggal.setHours(0, 0, 0, 0);

            const matchSearch = rowText.includes(searchText);
            const matchJenis = jenisValue === "" || rowJenis === jenisValue;
            const matchRole = roleValue === "" || rowRole === roleValue;

            let matchTanggal = true;
            if (startDate && endDate) {
                matchTanggal = rowTanggal >= startDate && rowTanggal <= endDate;
            }

            if (matchSearch && matchJenis && matchRole && matchTanggal) {
                row.style.display = "";
                visibleCount++;
            } else {
                row.style.display = "none";
            }
        });

        if (emptyStateRow) {
            emptyStateRow.style.display = visibleCount === 0 ? "" : "none";
        }
    }

    if (searchInput) searchInput.addEventListener("input", filterTable);
    if (filterJenis) filterJenis.addEventListener("change", filterTable);
    if (filterRole) filterRole.addEventListener("change", filterTable);

    window.exportTable = function () {
        alert("Fitur export belum diimplementasikan.");
    };
});
