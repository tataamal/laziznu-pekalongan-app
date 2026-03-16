import ApexCharts from 'apexcharts';

document.addEventListener("DOMContentLoaded", function () {
    const chartDataElement = document.getElementById("pc-chart-data");

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

    // 1. Trend Chart MWC & Ranting (Area Chart)
    const trendMwcRantingOptions = {
        series: [{
            name: 'Pemasukan MWC',
            data: chartData.trend.mwc
        }, {
            name: 'Pemasukan Ranting',
            data: chartData.trend.ranting
        }],
        chart: {
            height: 350,
            type: 'area',
            fontFamily: 'inherit',
            toolbar: { show: false }
        },
        colors: [palette.primary, palette.soft],
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
        stroke: { curve: 'smooth', width: 3 },
        xaxis: {
            categories: chartData.trend.labels,
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return new Intl.NumberFormat('id-ID', { notation: 'compact', compactDisplay: 'short' }).format(val);
                }
            }
        },
        tooltip: {
            theme: 'light',
            y: {
                formatter: function (val) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
                }
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'center',
            fontFamily: 'inherit',
            fontSize: '13px',
            fontWeight: 500,
            labels: { colors: "#64748b" },
            markers: { radius: 6, width: 8, height: 8, offsetX: -4 }
        }
    };
    
    if (document.querySelector("#trendChartMwcRanting")) {
        new ApexCharts(document.querySelector("#trendChartMwcRanting"), trendMwcRantingOptions).render();
    }

    // 2. Trend Chart PC (Area Chart - Income vs Expense)
    const trendPcOptions = {
        series: [{
            name: 'Pemasukan PC',
            data: chartData.trend.pc_income
        }, {
            name: 'Pengeluaran PC',
            data: chartData.trend.pc_expense
        }],
        chart: {
            height: 350,
            type: 'area',
            fontFamily: 'inherit',
            toolbar: { show: false }
        },
        colors: [palette.primaryDark, "#ef4444"], // Primary dark for income, red for expense
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
        stroke: { curve: 'smooth', width: 3 },
        xaxis: {
            categories: chartData.trend.labels,
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return new Intl.NumberFormat('id-ID', { notation: 'compact', compactDisplay: 'short' }).format(val);
                }
            }
        },
        tooltip: {
            theme: 'light',
            y: {
                formatter: function (val) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
                }
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'center',
            fontFamily: 'inherit',
            fontSize: '13px',
            fontWeight: 500,
            labels: { colors: "#64748b" },
            markers: { radius: 6, width: 8, height: 8, offsetX: -4 }
        }
    };
    
    if (document.querySelector("#trendChartPc")) {
        new ApexCharts(document.querySelector("#trendChartPc"), trendPcOptions).render();
    }

    // 3. Donut Chart (Distribusi Pengeluaran Infaq PC Only)
    const hasDistData = chartData.distribution.data && chartData.distribution.data.length > 0;
    
    // Minimal & Premium Colors
    const donutColors = [
        palette.primary,    // Emerald Green
        "#0284c7",          // Sky Blue
        "#7c3aed",          // Violet
        "#f59e0b",          // Amber
        "#ef4444",          // Rose
        "#14b8a6"           // Teal
    ];

    const donutOptions = {
        series: hasDistData ? chartData.distribution.data : [1],
        labels: hasDistData ? chartData.distribution.labels : ['Belum ada pengeluaran'],
        chart: {
            type: 'donut',
            height: 380,
            fontFamily: 'Inter, sans-serif',
        },
        colors: hasDistData ? donutColors : ["#f1f5f9"],
        plotOptions: {
            pie: {
                donut: {
                    size: '72%',
                    labels: {
                        show: true,
                        name: { 
                            show: true, 
                            fontSize: '14px', 
                            fontWeight: 500,
                            color: "#64748b",
                            offsetY: -4
                        },
                        value: {
                            show: true,
                            fontSize: '22px',
                            fontWeight: 700,
                            color: "#1e293b",
                            offsetY: 8,
                            formatter: function (val) {
                                if (!hasDistData) return "Rp 0";
                                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
                            }
                        },
                        total: {
                            show: true,
                            showAlways: true,
                            label: 'Total Pengeluaran',
                            fontSize: '13px',
                            fontWeight: 500,
                            color: "#94a3b8",
                            formatter: function (w) {
                                if (!hasDistData) return "Rp 0";
                                const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(total);
                            }
                        }
                    }
                }
            }
        },
        dataLabels: { enabled: false },
        stroke: { show: true, width: 2, colors: [palette.white] }, // Subtle white gap
        legend: {
            position: 'bottom',
            fontFamily: 'inherit',
            fontSize: '13px',
            fontWeight: 400,
            labels: { colors: "#64748b" },
            markers: { radius: 6, width: 8, height: 8, offsetX: -4 },
            itemMargin: { horizontal: 10, vertical: 5 }
        },
        tooltip: {
            enabled: true,
            theme: 'dark',
            style: { fontSize: '12px' },
            onDatasetHover: { highlightDataSeries: true },
            y: {
                formatter: function (val) {
                    if (!hasDistData) return "Rp 0";
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
                }
            }
        },
        states: {
            hover: { filter: { type: 'darken', value: 0.9 } }
        }
    };

    if (document.querySelector("#donutChartDistribution")) {
        new ApexCharts(document.querySelector("#donutChartDistribution"), donutOptions).render();
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
