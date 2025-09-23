// dashboard/assets/js/charts/ptk-chart.js
(function() {
    const ctx = document.getElementById('ptkChart');
    if (!ctx) return;

    const ptkChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: [],
            datasets: [{
                label: 'Guru',
                data: [],
                backgroundColor: ['#6777ef', '#3abaf4', '#ffa426', '#fc544b', '#34395e', '#28a745']
            }, {
                label: 'Tendik',
                data: [],
                backgroundColor: ['#6777ef99', '#3abaf499', '#ffa42699', '#fc544b99', '#34395e99', '#28a74599']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 15
                    }
                },
                tooltip: {
                    callbacks: {
                        label: (context) => {
                            const label = context.dataset.label;
                            const jenjang = context.label;
                            const value = context.parsed;
                            return `${label} (${jenjang}): ${window.analyticsDashboard.formatNumber(value)}`;
                        }
                    }
                }
            }
        }
    });

    // Attach to the main dashboard object
    window.analyticsDashboard.charts.ptk = ptkChart;
    window.analyticsDashboard.loadPTKData = async function() {
        const params = new URLSearchParams({ type: 'ptk', ...this.currentFilters });
        const data = await this.fetchAPI(`api/chart-data.php?${params}`);

        if (data && data.length > 0) {
            const labels = data.map(item => item.jenjang_pendidikan || 'N/A');
            const guruData = data.map(item => parseInt(item.total_guru) || 0);
            const tendikData = data.map(item => parseInt(item.total_tendik) || 0);

            this.charts.ptk.data.labels = labels;
            this.charts.ptk.data.datasets[0].data = guruData;
            this.charts.ptk.data.datasets[1].data = tendikData;
        } else {
            this.charts.ptk.data.labels = ['Tidak ada data'];
            this.charts.ptk.data.datasets[0].data = [1];
            this.charts.ptk.data.datasets[1].data = [0];
        }
        this.charts.ptk.update();
    };
})();