// dashboard/assets/js/charts/trend-chart.js
(function() {
    const ctx = document.getElementById('trendChart');
    if (!ctx) return;

    const trendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Data Sukses',
                data: [],
                borderColor: '#28a745',
                tension: 0.3
            }, {
                label: 'Data Gagal',
                data: [],
                borderColor: '#dc3545',
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            }
        }
    });

    window.analyticsDashboard.charts.trend = trendChart;
    window.analyticsDashboard.loadTrendData = async function() {
        // This chart doesn't use filters, so it fetches directly
        const data = await this.fetchAPI(`api/trend-data.php`);

        if (data && data.length > 0) {
            const labels = data.map(item => item.date);
            const successData = data.map(item => item.total_success);
            const failedData = data.map(item => item.total_failed);

            this.charts.trend.data.labels = labels;
            this.charts.trend.data.datasets[0].data = successData;
            this.charts.trend.data.datasets[1].data = failedData;
        } else {
            this.charts.trend.data.labels = ['Data log tidak ditemukan'];
            this.charts.trend.data.datasets[0].data = [];
            this.charts.trend.data.datasets[1].data = [];
        }
        this.charts.trend.update();
    };
})();