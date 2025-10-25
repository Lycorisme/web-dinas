document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});

// Function to export table to Excel
function exportTableToExcel() {
    iziToast.info({
        title: 'Info',
        message: 'Fitur export akan segera tersedia',
        position: 'topRight'
    });
}

// Function to view detail for wilayah
function viewDetail(nama, kecamatan, sekolah) {
    iziToast.info({
        title: nama,
        message: `Kecamatan: ${kecamatan} | Sekolah: ${sekolah}`,
        position: 'topRight',
        timeout: 5000
    });
}

// Function to format numbers
function formatNumber(num) {
    if (num === null || num === undefined) return '0';
    return new Intl.NumberFormat('id-ID').format(num);
}

// Function to get marker color for map
function getMarkerColor(jenjang) {
    const colors = {
        'SD': '#28a745',
        'SMP': '#ffc107',
        'SMA': '#007bff',
        'SMK': '#6f42c1',
        'TK': '#fd7e14',
        'PAUD': '#e83e8c'
    };
    return colors[jenjang] || '#6c757d';
}

// Function to initialize map
function initializeMap() {
    let map = L.map('school-map').setView([-3.0927, 115.2838], 8);
    
    let mapTileLayers = {
        default: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }),
        dark: L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; Stadia Maps, OpenMapTiles, OpenStreetMap contributors'
        }),
        satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri'
        })
    };
    
    mapTileLayers.default.addTo(map);
    let mapLayers = L.layerGroup().addTo(map);
    
    loadMapData(map, mapLayers);
    
    ['default', 'dark', 'satellite'].forEach(theme => {
        const btn = document.getElementById(`theme-${theme}`);
        if (btn) {
            btn.addEventListener('click', () => changeMapTheme(map, mapTileLayers, theme));
        }
    });
    
    const refreshMapBtn = document.getElementById('refresh-map');
    if (refreshMapBtn) {
        refreshMapBtn.addEventListener('click', () => loadMapData(map, mapLayers));
    }
    
    return { map, mapTileLayers, mapLayers };
}

// Function to change map theme
function changeMapTheme(map, mapTileLayers, theme) {
    Object.values(mapTileLayers).forEach(layer => {
        if (map.hasLayer(layer)) {
            map.removeLayer(layer);
        }
    });
    mapTileLayers[theme].addTo(map);
    updateThemeButton(theme);
}

// Function to update theme button
function updateThemeButton(activeTheme) {
    ['default', 'dark', 'satellite'].forEach(theme => {
        const btn = document.getElementById(`theme-${theme}`);
        if (btn) {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline-secondary');
            if (theme === activeTheme) {
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-primary');
            }
        }
    });
}

// Function to load map data
function loadMapData(map, mapLayers) {
    const mapContainer = document.getElementById('school-map');
    if (!mapContainer) return;
    
    const existingLoader = mapContainer.querySelector('.map-loading');
    if (existingLoader) existingLoader.remove();

    const loadingEl = document.createElement('div');
    loadingEl.className = 'map-loading';
    loadingEl.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>';
    mapContainer.appendChild(loadingEl);

    // Simulate loading data since we don't have the actual API
    setTimeout(() => {
        mapLayers.clearLayers();
        
        // Add some sample markers for demonstration
        const sampleSchools = [
            {lat: -3.3194, lng: 114.5926, nama: "SMA Negeri 1 Banjarmasin", jenjang: "SMA", status: "Negeri", npsn: "12345678", siswa: 850, ptk: 65, rombel: 27, alamat: "Jl. Pangeran Antasari", kecamatan: "Banjarmasin Tengah"},
            {lat: -3.3167, lng: 114.5907, nama: "SMP Negeri 1 Banjarmasin", jenjang: "SMP", status: "Negeri", npsn: "23456789", siswa: 720, ptk: 45, rombel: 24, alamat: "Jl. Ahmad Yani", kecamatan: "Banjarmasin Tengah"},
            {lat: -3.3225, lng: 114.5956, nama: "SD Negeri 1 Banjarmasin", jenjang: "SD", status: "Negeri", npsn: "34567890", siswa: 450, ptk: 25, rombel: 15, alamat: "Jl. S. Parman", kecamatan: "Banjarmasin Tengah"},
            {lat: -3.3250, lng: 114.5980, nama: "SMK Negeri 1 Banjarmasin", jenjang: "SMK", status: "Negeri", npsn: "45678901", siswa: 680, ptk: 55, rombel: 22, alamat: "Jl. Gatot Subroto", kecamatan: "Banjarmasin Timur"},
            {lat: -2.9500, lng: 115.2500, nama: "SMA Negeri 1 Martapura", jenjang: "SMA", status: "Negeri", npsn: "56789012", siswa: 780, ptk: 60, rombel: 26, alamat: "Jl. Jenderal Sudirman", kecamatan: "Martapura"},
        ];

        const markers = [];
        let bounds = L.latLngBounds();
        
        sampleSchools.forEach(school => {
            if (school.lat && school.lng && school.lat !== 0 && school.lng !== 0) {
                const marker = createSchoolMarker(school);
                markers.push(marker);
                bounds.extend([school.lat, school.lng]);
            }
        });
        
        if (markers.length > 0) {
            markers.forEach(marker => marker.addTo(mapLayers));
            if (bounds.isValid()) {
                map.fitBounds(bounds, { padding: [20, 20] });
            }
        }
        
        const loader = mapContainer.querySelector('.map-loading');
        if (loader) loader.remove();
    }, 1500);
}

// Function to create school marker
function createSchoolMarker(school) {
    const markerColor = getMarkerColor(school.jenjang);
    const iconHtml = `<i class="fas fa-map-marker-alt" style="color: ${markerColor}; font-size: 24px;"></i>`;
    
    const customIcon = L.divIcon({
        html: iconHtml,
        iconSize: [24, 24],
        iconAnchor: [12, 24],
        popupAnchor: [0, -24],
        className: 'custom-marker'
    });

    const marker = L.marker([school.lat, school.lng], { icon: customIcon });
    
    const popupContent = `
        <div class="school-popup">
            <h6 class="mb-2">${school.nama}</h6>
            <div class="popup-info">
                <p class="mb-1"><strong>NPSN:</strong> ${school.npsn}</p>
                <p class="mb-1"><strong>Jenjang:</strong> ${school.jenjang} (${school.status})</p>
                <p class="mb-1"><strong>Siswa:</strong> ${formatNumber(school.siswa)} | <strong>Rombel:</strong> ${school.rombel}</p>
                <p class="mb-1"><strong>PTK:</strong> ${formatNumber(school.ptk)} orang</p>
                <p class="mb-0"><strong>Alamat:</strong> ${school.alamat}, ${school.kecamatan}</p>
            </div>
        </div>
    `;
    
    marker.bindPopup(popupContent, {
        maxWidth: 300,
        className: 'custom-popup'
    });
    
    return marker;
}

// Function to initialize charts
function initializeCharts() {
    const statistikDataElement = document.getElementById('statistik-data');
    if (!statistikDataElement) return;
    
    const statistikData = JSON.parse(statistikDataElement.textContent);
    
    // Chart Jenjang
    const ctxJenjang = document.getElementById('chartJenjang');
    if (ctxJenjang) {
        new Chart(ctxJenjang.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: statistikData.jenjang.map(d => d.jenjang_pendidikan),
                datasets: [{
                    data: statistikData.jenjang.map(d => d.total),
                    backgroundColor: ['#28a745', '#ffc107', '#007bff', '#6f42c1'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: 'white'
                        }
                    }
                }
            }
        });
    }
    
    // Chart Status
    const ctxStatus = document.getElementById('chartStatus');
    if (ctxStatus) {
        new Chart(ctxStatus.getContext('2d'), {
            type: 'pie',
            data: {
                labels: statistikData.status.map(d => d.status_sekolah),
                datasets: [{
                    data: statistikData.status.map(d => d.total),
                    backgroundColor: ['#17a2b8', '#343a40'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: 'white'
                        }
                    }
                }
            }
        });
    }
    
    // Chart Kabupaten
    const ctxKabupaten = document.getElementById('chartKabupaten');
    if (ctxKabupaten) {
        new Chart(ctxKabupaten.getContext('2d'), {
            type: 'bar',
            data: {
                labels: statistikData.kabupaten.map(d => d.nama_kabupaten),
                datasets: [{
                    label: 'Jumlah Sekolah',
                    data: statistikData.kabupaten.map(d => d.total),
                    backgroundColor: '#6777ef',
                    borderColor: '#6777ef',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: 'white'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: 'white'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
}