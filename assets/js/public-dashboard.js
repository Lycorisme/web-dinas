// Public Dashboard JavaScript
 $(document).ready(function() {
    // Initialize charts
    const chartDataElement = document.getElementById('chart-data');
    let jenjangData = null;
    
    if (chartDataElement) {
        try {
            jenjangData = JSON.parse(chartDataElement.textContent);
        } catch (e) {
            console.error('Error parsing chart data:', e);
        }
    }
    
    if (jenjangData && jenjangData.length > 0) {
        const ctx = document.getElementById('jenjangChart');
        if (ctx) {
            const chartCtx = ctx.getContext('2d');
            const jenjangChart = new Chart(chartCtx, {
                type: 'doughnut',
                data: {
                    labels: jenjangData.map(item => item.jenjang_pendidikan),
                    datasets: [{
                        data: jenjangData.map(item => item.total),
                        backgroundColor: ['#6777ef', '#28a745', '#ffc107', '#17a2b8', '#dc3545', '#6610f2'],
                        borderWidth: 0,
                        hoverOffset: 15,
                        hoverBorderWidth: 3,
                        hoverBorderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${label}: ${new Intl.NumberFormat('id-ID').format(value)} sekolah (${percentage}%)`;
                                }
                            },
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(255,255,255,0.2)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            displayColors: true
                        }
                    },
                    animation: {
                        animateRotate: true,
                        duration: 2000,
                        easing: 'easeOutCubic'
                    },
                    interaction: {
                        intersect: false,
                        mode: 'nearest'
                    }
                }
            });
        }
    }
    
    // Initialize map if available
    if (typeof L !== 'undefined') {
        const mapDataElement = document.getElementById('map-data');
        let mapData = null;
        
        if (mapDataElement) {
            try {
                mapData = JSON.parse(mapDataElement.textContent);
            } catch (e) {
                console.error('Error parsing map data:', e);
            }
        }
        
        if (mapData && mapData.length > 0) {
            // Initialize map centered on Kalimantan Selatan
            const map = L.map('school-map').setView([-3.0926, 115.2838], 8);
            
            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Add markers for each school
            mapData.forEach(school => {
                if (school.lintang && school.bujur) {
                    const markerColor = getMarkerColor(school.jenjang_pendidikan);
                    const iconHtml = `<i class="fas fa-map-marker-alt" style="color: ${markerColor}; font-size: 24px;"></i>`;
                    
                    const customIcon = L.divIcon({
                        html: iconHtml,
                        iconSize: [24, 24],
                        iconAnchor: [12, 24],
                        popupAnchor: [0, -24],
                        className: 'custom-marker'
                    });

                    const marker = L.marker([school.lintang, school.bujur], { icon: customIcon });
                    
                    const popupContent = `
                        <div class="school-popup">
                            <h6 class="mb-2">${school.nama_sekolah}</h6>
                            <div class="popup-info">
                                <p class="mb-1"><strong>NPSN:</strong> ${school.npsn}</p>
                                <p class="mb-1"><strong>Jenjang:</strong> ${school.jenjang_pendidikan} (${school.status_sekolah})</p>
                                <p class="mb-0"><strong>Alamat:</strong> ${school.alamat_jalan}, ${school.nama_kecamatan}</p>
                            </div>
                        </div>
                    `;
                    
                    marker.bindPopup(popupContent, {
                        maxWidth: 300,
                        className: 'custom-popup'
                    });
                    
                    marker.addTo(map);
                }
            });
        }
    }
    
    function getMarkerColor(jenjang) {
        const colors = {
            'SD': '#28a745',    // green
            'SMP': '#ffc107',   // yellow/orange
            'SMA': '#007bff',   // blue
            'SMK': '#6f42c1',   // purple
            'TK': '#fd7e14',    // orange
            'PAUD': '#e83e8c'   // pink
        };
        return colors[jenjang] || '#6c757d'; // gray as default
    }
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Format numbers
    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }
});