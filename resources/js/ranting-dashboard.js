import ApexCharts from 'apexcharts';

document.addEventListener("DOMContentLoaded", function () {
    const chartDataElement = document.getElementById("ranting-chart-data");

    if (!chartDataElement) return;

    const chartData = JSON.parse(chartDataElement.textContent);

    const palette = {
        primary: "#005a26",
        primaryDark: "#00471e",
        soft: "#90bc68",
        soft2: "#9bc676",
        soft3: "#b7d59a",
        soft4: "#d7e8c8",
        white: "#ffffff",
    };

    // 1. Line Chart Income (Pemasukan Bulanan)
    const incomeLineOptions = {
        series: [{
            name: 'Pemasukan',
            data: chartData.bar.data
        }],
        chart: {
            height: 300,
            type: 'area', // Area chart gives a more modern look resembling a filled line chart
            fontFamily: 'inherit',
            toolbar: { show: false }
        },
        colors: [palette.primary],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        dataLabels: { enabled: false },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        xaxis: {
            categories: chartData.bar.labels,
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return new Intl.NumberFormat('id-ID', { notation: 'compact', compactDisplay: 'short', maximumFractionDigits: 1 }).format(val);
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
                }
            }
        }
    };
    
    if (document.querySelector("#lineChartIncome")) {
        new ApexCharts(document.querySelector("#lineChartIncome"), incomeLineOptions).render();
    }

    // 2. Donut Chart (Distribusi Jenis Infaq)
    const donutOptions = {
        series: chartData.pie.data,
        labels: chartData.pie.labels,
        chart: {
            type: 'donut',
            height: 300,
            fontFamily: 'inherit',
        },
        colors: chartData.pie.isEmpty 
            ? ["#e4e4e7"] // slate-200 for empty state
            : [palette.primary, palette.soft, palette.primaryDark, palette.soft2, palette.soft3],
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: true,
                        name: { show: true, fontSize: '14px', fontWeight: 600 },
                        value: {
                            show: true,
                            fontSize: '16px',
                            fontWeight: 700,
                            formatter: function (val) {
                                if (chartData.pie.isEmpty) return "0";
                                return new Intl.NumberFormat('id-ID', { notation: 'compact', compactDisplay: 'short' }).format(val);
                            }
                        },
                        total: {
                            show: true,
                            showAlways: true,
                            label: 'Total',
                            fontSize: '14px',
                            formatter: function (w) {
                                if (chartData.pie.isEmpty) return "0";
                                const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                return new Intl.NumberFormat('id-ID', { notation: 'compact', compactDisplay: 'short' }).format(total);
                            }
                        }
                    }
                }
            }
        },
        dataLabels: { enabled: false },
        stroke: { show: false },
        legend: {
            position: 'bottom',
            offsetY: 0,
            markers: { radius: 12 },
            show: !chartData.pie.isEmpty
        },
        tooltip: {
            enabled: !chartData.pie.isEmpty,
            y: {
                formatter: function (val) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
                }
            }
        }
    };

    if (document.querySelector("#donutChartDistribution")) {
        new ApexCharts(document.querySelector("#donutChartDistribution"), donutOptions).render();
    }

    // 3. Trend Dual Column Chart (Trend Pemasukan vs Pengeluaran)
    const trendOptions = {
        series: [{
            name: 'Pemasukan (Net Income)',
            data: chartData.trend.income.data
        }, {
            name: 'Pengeluaran (Cost Amount)',
            data: chartData.trend.distribution.data
        }],
        chart: {
            type: 'bar',
            height: 350,
            fontFamily: 'inherit',
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '45%',
                borderRadius: 4
            },
        },
        dataLabels: { enabled: false },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: chartData.trend.labels,
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return new Intl.NumberFormat('id-ID', { notation: 'compact', compactDisplay: 'short', maximumFractionDigits: 1 }).format(val);
                }
            }
        },
        colors: [palette.primary, palette.soft],
        fill: { opacity: 1 },
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function (val, opts) {
                    const idx = opts.dataPointIndex;
                    const dsIdx = opts.seriesIndex;
                    const amount = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
                    
                    if (dsIdx === 1) { // Pengeluaran
                        const type = chartData.trend.distribution.labels[idx];
                        if (type && type !== '-') {
                            return amount + ' (' + type + ')';
                        }
                    }
                    return amount;
                }
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'center',
            markers: { radius: 12 }
        }
    };

    if (document.querySelector("#trendBarChart")) {
        new ApexCharts(document.querySelector("#trendBarChart"), trendOptions).render();
    }

    // Table Multi-Filter Data
    const searchInput = document.getElementById("search");
    const filterJenis = document.getElementById("filterJenis");
    const filterRole = document.getElementById("filterRole");
    const filterTanggal = document.getElementById("filterTanggal");
    const tableRows = document.querySelectorAll("#dataTable tbody tr.trx-row");
    const emptyStateRow = document.querySelector("#dataTable tbody tr:not(.trx-row)");

    let flatpickrInstance = null;
    if (filterTanggal) {
        flatpickrInstance = flatpickr(filterTanggal, {
            mode: "range",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d M Y",
            onChange: function() {
                filterTable();
            }
        });
    }

    function filterTable() {
        if (!tableRows.length) return;

        const searchText = searchInput ? searchInput.value.toLowerCase() : "";
        const jenisValue = filterJenis ? filterJenis.value.toLowerCase() : "";
        const roleValue = filterRole ? filterRole.value.toLowerCase() : "";
        
        // Handle Flatpickr Range
        let startDate = null;
        let endDate = null;
        if (flatpickrInstance && flatpickrInstance.selectedDates.length > 0) {
            startDate = flatpickrInstance.selectedDates[0];
            startDate.setHours(0,0,0,0);
            if (flatpickrInstance.selectedDates.length > 1) {
                endDate = flatpickrInstance.selectedDates[1];
                endDate.setHours(23,59,59,999);
            } else {
                // If only one date selected, treat end date as same day
                endDate = new Date(startDate);
                endDate.setHours(23,59,59,999);
            }
        }

        let visibleCount = 0;

        tableRows.forEach((row) => {
            const rowText = row.textContent.toLowerCase();
            const rowJenis = row.getAttribute('data-jenis');
            const rowRole = row.getAttribute('data-role');
            const rowTanggalStr = row.getAttribute('data-tanggal');
            const rowTanggal = new Date(rowTanggalStr);
            rowTanggal.setHours(0,0,0,0);

            // Check if matches each criteria (empty string means "all")
            const matchSearch = rowText.includes(searchText);
            const matchJenis = jenisValue === "" || rowJenis === jenisValue;
            const matchRole = roleValue === "" || rowRole === roleValue;
            
            // Match Tanggal Range
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

        // Show empty state if no rows match
        if (emptyStateRow) {
            emptyStateRow.style.display = visibleCount === 0 ? "" : "none";
        }
    }

    // Attach event listeners to all filter inputs
    if (searchInput) searchInput.addEventListener("input", filterTable);
    if (filterJenis) filterJenis.addEventListener("change", filterTable);
    if (filterRole) filterRole.addEventListener("change", filterTable);

    window.exportTable = function () {
        alert("Fitur export belum diimplementasikan.");
    };
});
