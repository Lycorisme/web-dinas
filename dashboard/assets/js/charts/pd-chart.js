// dashboard/assets/js/charts/pd-chart.js
(function() {
    const ctx = document.getElementById('pdChart');
    if (!ctx) return;

    const pdChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Laki-laki',
                data: [],
                backgroundColor: '#3abaf4',
                borderColor: '#3abaf4',
                borderWidth: 1
            }, {
                label: 'Perempuan',
                data: [],
                backgroundColor: '#fc544b',
                borderColor: '#fc544b',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    stacked: false
                },
                y: {
                    beginAtZero: true,
                    stacked: false,
                    ticks: { 
                        callback: (value) => window.analyticsDashboard.formatNumber(value)
                    }
                }
            },
            plugins: {
                legend: { 
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15
                    }
                },
                tooltip: {
                    callbacks: {
                        label: (context) => `${context.dataset.label}: ${window.analyticsDashboard.formatNumber(context.parsed.y)} siswa`
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    window.analyticsDashboard.charts.pd = pdChart;
    window.analyticsDashboard.loadPDData = async function() {
        const params = new URLSearchParams({ type: 'pd', ...this.currentFilters });
        const data = await this.fetchAPI(`api/chart-data.php?${params}`);

        if (data && data.length > 0) {
            const labels = data.map(item => item.jenjang_pendidikan || 'N/A');
            const lakiData = data.map(item => parseInt(item.total_laki) || 0);
            const perempuanData = data.map(item => parseInt(item.total_perempuan) || 0);

            this.charts.pd.data.labels = labels;
            this.charts.pd.data.datasets[0].data = lakiData;
            this.charts.pd.data.datasets[1].data = perempuanData;
        } else {
            this.charts.pd.data.labels = ['Tidak ada data'];
            this.charts.pd.data.datasets[0].data = [0];
            this.charts.pd.data.datasets[1].data = [0];
        }
        this.charts.pd.update();
    };
})();