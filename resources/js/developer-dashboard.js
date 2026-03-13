document.addEventListener("DOMContentLoaded", function () {
    const chartDataElement = document.getElementById("developer-chart-data");

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
        lineFill: "rgba(144, 188, 104, 0.20)",
        barFill: "rgba(0, 90, 38, 0.85)",
    };

    const commonLegendLabelColor = "#102413";
    const commonGridColor = "rgba(16, 36, 19, 0.08)";

    const barCanvas = document.getElementById("barChart");
    if (barCanvas) {
        const barCtx = barCanvas.getContext("2d");
        new Chart(barCtx, {
            type: "bar",
            data: {
                labels: chartData.bar.labels,
                datasets: [
                    {
                        label: "Pemasukan (Rp)",
                        data: chartData.bar.data,
                        backgroundColor: [
                            palette.primary,
                            palette.primaryDark,
                            palette.soft,
                            palette.soft2,
                            palette.primary,
                            palette.soft3,
                        ],
                        borderColor: [
                            palette.primaryDark,
                            palette.primary,
                            palette.primaryDark,
                            palette.primary,
                            palette.primaryDark,
                            palette.primaryDark,
                        ],
                        borderWidth: 1.5,
                        borderRadius: 6,
                        borderSkipped: false,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                        labels: {
                            color: commonLegendLabelColor,
                        },
                    },
                },
                scales: {
                    x: {
                        ticks: {
                            color: commonLegendLabelColor,
                        },
                        grid: {
                            display: false,
                        },
                    },
                    y: {
                        ticks: {
                            color: commonLegendLabelColor,
                        },
                        grid: {
                            color: commonGridColor,
                        },
                    },
                },
                animation: {
                    duration: 1500,
                    easing: "easeInOutQuad",
                },
            },
        });
    }

    const pieCanvas = document.getElementById("pieChart");
    if (pieCanvas) {
        const pieCtx = pieCanvas.getContext("2d");
        new Chart(pieCtx, {
            type: "pie",
            data: {
                labels: chartData.pie.labels,
                datasets: [
                    {
                        data: chartData.pie.data,
                        backgroundColor: [
                            palette.primary,
                            palette.primaryDark,
                            palette.soft,
                            palette.soft2,
                            palette.soft3,
                        ],
                        borderWidth: 3,
                        borderColor: palette.white,
                        hoverOffset: 8,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: "bottom",
                        labels: {
                            color: commonLegendLabelColor,
                            padding: 16,
                        },
                    },
                },
                animation: {
                    duration: 1500,
                    easing: "easeInOutQuad",
                },
            },
        });
    }

    const lineCanvas = document.getElementById("lineChart");
    if (lineCanvas) {
        const lineCtx = lineCanvas.getContext("2d");
        new Chart(lineCtx, {
            type: "line",
            data: {
                labels: chartData.line.labels,
                datasets: [
                    {
                        label: "Pengguna Baru",
                        data: chartData.line.data,
                        borderColor: palette.primary,
                        backgroundColor: palette.lineFill,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: palette.soft,
                        pointBorderColor: palette.primaryDark,
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                        labels: {
                            color: commonLegendLabelColor,
                        },
                    },
                },
                scales: {
                    x: {
                        ticks: {
                            color: commonLegendLabelColor,
                        },
                        grid: {
                            display: false,
                        },
                    },
                    y: {
                        ticks: {
                            color: commonLegendLabelColor,
                        },
                        grid: {
                            color: commonGridColor,
                        },
                    },
                },
                animation: {
                    duration: 1500,
                    easing: "easeInOutQuad",
                },
            },
        });
    }

    const searchInput = document.getElementById("search");
    if (searchInput) {
        searchInput.addEventListener("input", function () {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll("#dataTable tbody tr");

            rows.forEach((row) => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? "" : "none";
            });
        });
    }

    window.exportTable = function () {
        alert("Fitur export belum diimplementasikan.");
    };
});
