// dashboard/assets/js/charts/rombel-chart.js
(function() {
    const ctx = document.getElementById('rombelChart');
    if (!ctx) return;

    const rombelChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Total Siswa',
                data: [],
                borderColor: '#6777ef',
                backgroundColor: 'rgba(103, 119, 239, 0.2)',
                fill: true,
                yAxisID: 'ySiswa'
            }, {
                label: 'Jumlah Rombel',
                data: [],
                borderColor: '#ffa426',
                backgroundColor: 'rgba(255, 164, 38, 0.2)',
                fill: true,
                yAxisID: 'yRombel'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                ySiswa: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    title: { display: true, text: 'Total Siswa' },
                    ticks: { callback: (value) => window.analyticsDashboard.formatNumber(value) }
                },
                yRombel: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    title: { display: true, text: 'Jumlah Rombel' },
                    grid: { drawOnChartArea: false } // only draw grid for first Y axis
                }
            },
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: (context) => `${context.dataset.label}: ${window.analyticsDashboard.formatNumber(context.parsed.y)}`
                    }
                }
            }
        }
    });

    window.analyticsDashboard.charts.rombel = rombelChart;
    window.analyticsDashboard.loadRombelData = async function() {
        const params = new URLSearchParams({ type: 'rombel', ...this.currentFilters });
        const data = await this.fetchAPI(`api/chart-data.php?${params}`);

        if (data && data.length > 0) {
            const labels = data.map(item => `Kelas ${item.tingkat_kelas}`);
            const siswaData = data.map(item => parseInt(item.total_siswa) || 0);
            const rombelData = data.map(item => parseInt(item.jumlah_rombel) || 0);

            this.charts.rombel.data.labels = labels;
            this.charts.rombel.data.datasets[0].data = siswaData;
            this.charts.rombel.data.datasets[1].data = rombelData;
        } else {
            this.charts.rombel.data.labels = ['Data tidak ditemukan'];
            this.charts.rombel.data.datasets[0].data = [];
            this.charts.rombel.data.datasets[1].data = [];
        }
        this.charts.rombel.update();
    };
})();