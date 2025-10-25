document.addEventListener('DOMContentLoaded', function () {
    // =================================================================
    // DOM ELEMENTS
    // =================================================================
    const filterKabupaten = document.getElementById('filterKabupaten');
    const filterKecamatan = document.getElementById('filterKecamatan');
    const filterButton = document.getElementById('filterButton');
    
    const statCards = {
        sekolah: document.getElementById('totalSekolah'),
        siswa: document.getElementById('totalSiswa'),
        guru: document.getElementById('totalGuru'),
        rasio: document.getElementById('rasioSiswaGuru'),
    };
    
    // =================================================================
    // CHART.JS & LEAFLET MAP INSTANCES
    // =================================================================
    let jenjangChart, statusChart;
    let map = L.map('school-map').setView([-3.0927, 115.2838], 8);
    let mapLayers = L.layerGroup().addTo(map);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // =================================================================
    // INITIALIZATION
    // =================================================================
    function initialize() {
        fetchKabupaten();
        fetchAllData(true); 
    }

    // =================================================================
    // EVENT LISTENERS
    // =================================================================
    filterKabupaten.addEventListener('change', function () {
        const kabId = this.value;
        filterKecamatan.disabled = true;
        filterKecamatan.innerHTML = '<option value="0">Memuat...</option>';
        
        if (kabId === '0') {
            filterKecamatan.innerHTML = '<option value="0">Semua Kecamatan</option>';
            filterKecamatan.disabled = false;
        } else {
            fetchKecamatan(kabId);
        }
    });

    filterButton.addEventListener('click', () => fetchAllData(false));

    // =================================================================
    // DATA FETCHING & UI UPDATE FUNCTIONS
    // =================================================================
    function fetchKabupaten() {
        fetch('get_kabupaten.php')
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    filterKabupaten.innerHTML = '<option value="0">Semua Wilayah</option>';
                    result.data.forEach(kab => {
                        filterKabupaten.add(new Option(kab.nama_kabupaten, kab.id_kabupaten));
                    });
                }
            })
            .catch(error => console.error('Error fetching kabupaten:', error));
    }

    function fetchKecamatan(kabId) {
        fetch(`get_kecamatan.php?id_kabupaten=${kabId}`)
            .then(response => response.json())
            .then(result => {
                filterKecamatan.innerHTML = '<option value="0">Semua Kecamatan</option>';
                if (result.success) {
                    result.data.forEach(kec => {
                        filterKecamatan.add(new Option(kec.nama_kecamatan, kec.id_kecamatan));
                    });
                }
                filterKecamatan.disabled = false;
            })
            .catch(error => console.error('Error fetching kecamatan:', error));
    }

    function fetchAllData(isInitialLoad = false) {
        const kabId = filterKabupaten.value;
        const kecId = filterKecamatan.value;
        
        setLoadingState(true);
        
        const params = new URLSearchParams({ id_kabupaten: kabId, id_kecamatan: kecId });
        
        fetch(`get_statistik_data.php?${params.toString()}`)
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    updateUI(result, isInitialLoad);
                }
            })
            .catch(error => console.error('Error fetching statistik data:', error))
            .finally(() => setLoadingState(false));

        loadMapData(kabId, kecId);
    }

    function setLoadingState(isLoading) {
        filterButton.disabled = isLoading;
        filterButton.innerHTML = isLoading ? '<span class="spinner-border spinner-border-sm"></span> Loading...' : '<i class="lni lni-search-alt"></i> Terapkan';
    }
    
    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num || 0);
    }
    
    function updateUI(data, isInitialLoad) {
        statCards.sekolah.textContent = formatNumber(data.stats.totalSekolah);
        statCards.siswa.textContent = formatNumber(data.stats.totalSiswa);
        statCards.guru.textContent = formatNumber(data.stats.totalGuru);
        statCards.rasio.textContent = `1 : ${data.stats.rasioSiswaGuru}`;

        if (isInitialLoad) {
            initCharts(data.charts);
        } else {
            updateCharts(data.charts);
        }
    }

    // =================================================================
    // CHART & MAP RENDER/UPDATE FUNCTIONS
    // =================================================================
    function initCharts(data) {
        const chartOptions = { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { color: '#5B657E' } } } };

        jenjangChart = new Chart(document.getElementById('chartJenjang').getContext('2d'), { type: 'doughnut', data: { labels: data.jenjang.map(d => d.jenjang_pendidikan), datasets: [{ data: data.jenjang.map(d => d.total), backgroundColor: ['#28a745', '#ffc107', '#007bff', '#6f42c1'], borderColor: '#fff', borderWidth: 2 }] }, options: chartOptions });
        statusChart = new Chart(document.getElementById('chartStatus').getContext('2d'), { type: 'pie', data: { labels: data.status.map(d => d.status_sekolah), datasets: [{ data: data.status.map(d => d.total), backgroundColor: ['#17a2b8', '#343a40'], borderColor: '#fff', borderWidth: 2 }] }, options: chartOptions });
    }

    function updateCharts(data) {
        jenjangChart.data.labels = data.jenjang.map(d => d.jenjang_pendidikan);
        jenjangChart.data.datasets[0].data = data.jenjang.map(d => d.total);
        jenjangChart.update();

        statusChart.data.labels = data.status.map(d => d.status_sekolah);
        statusChart.data.datasets[0].data = data.status.map(d => d.total);
        statusChart.update();
    }

    function loadMapData(kabId = 0, kecId = 0) {
        const loadingEl = document.querySelector('.map-loading');
        loadingEl.style.display = 'flex';
        
        const params = new URLSearchParams({ id_kabupaten: kabId, id_kecamatan: kecId });

        fetch(`get_schools_for_map.php?${params.toString()}`)
            .then(response => response.json())
            .then(result => {
                mapLayers.clearLayers();
                if (result.success && result.data.length > 0) {
                    const bounds = L.latLngBounds();
                    result.data.forEach(school => {
                        if (school.lat && school.lng) {
                            createSchoolMarker(school).addTo(mapLayers);
                            bounds.extend([school.lat, school.lng]);
                        }
                    });
                    if (bounds.isValid()) {
                        map.fitBounds(bounds, { padding: [50, 50] });
                    }
                } else {
                    map.setView([-3.0927, 115.2838], 8);
                }
            })
            .catch(error => console.error('Error fetching map data:', error))
            .finally(() => {
                loadingEl.style.display = 'none';
            });
    }

    function createSchoolMarker(school) {
        const markerColor = { 'SD': '#28a745', 'SMP': '#ffc107', 'SMA': '#007bff', 'SMK': '#6f42c1' }[school.jenjang] || '#6c757d';
        const iconHtml = `<i class="fas fa-map-marker-alt" style="color: ${markerColor}; font-size: 24px; text-shadow: 0 0 3px rgba(0,0,0,0.5);"></i>`;
        const customIcon = L.divIcon({ html: iconHtml, className: 'custom-marker', iconSize: [24, 24], iconAnchor: [12, 24], popupAnchor: [0, -24] });
        
        const popupContent = `<h6>${school.nama}</h6><p class="mb-1"><strong>Jenjang:</strong> ${school.jenjang} (${school.status})</p><p class="mb-0"><strong>Siswa:</strong> ${formatNumber(school.siswa)}</p>`;
        
        const marker = L.marker([school.lat, school.lng], { icon: customIcon });
        marker.bindPopup(popupContent, { maxWidth: 300 });
        return marker;
    }
    
    // Start the process
    initialize();
});