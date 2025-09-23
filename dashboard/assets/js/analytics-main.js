// dashboard/assets/js/analytics-main.js
class AnalyticsDashboard {
    constructor() {
        this.currentFilters = {
            wilayah: '',
            jenjang: '',
            status: ''
        };
        this.charts = {};
        this.map = null;
        this.mapLayers = L.layerGroup();
        this.currentMapTheme = 'default';
        this.mapTileLayers = {};
        
        this.init();
    }
    
    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.loadInitialFilterOptions();
            this.initEventListeners();
            this.initMap();
            this.updateAllData();
        });
    }
    
    initEventListeners() {
        const applyBtn = document.getElementById('apply-filter');
        const wilayahSelect = document.getElementById('filter-wilayah');
        const jenjangSelect = document.getElementById('filter-jenjang');
        const statusSelect = document.getElementById('filter-status');

        if (applyBtn) {
            applyBtn.addEventListener('click', () => this.applyFilters());
        }

        // Cascade filter events
        if (wilayahSelect) {
            wilayahSelect.addEventListener('change', () => this.onWilayahChange());
        }

        if (jenjangSelect) {
            jenjangSelect.addEventListener('change', () => this.onJenjangChange());
        }

        // Map theme buttons
        ['default', 'dark', 'satellite'].forEach(theme => {
            const btn = document.getElementById(`theme-${theme}`);
            if (btn) {
                btn.addEventListener('click', () => this.changeMapTheme(theme));
            }
        });

        const refreshMapBtn = document.getElementById('refresh-map');
        if (refreshMapBtn) {
            refreshMapBtn.addEventListener('click', () => this.loadMapData());
        }
    }

    async onWilayahChange() {
        const wilayah = document.getElementById('filter-wilayah').value;
        
        // Clear dependent selects
        this.clearSelect('filter-jenjang', 'Semua Jenjang');
        this.clearSelect('filter-status', 'Semua Status');
        
        // Load jenjang options based on selected wilayah
        await this.loadJenjangOptions(wilayah);
        await this.loadStatusOptions(wilayah, '');
    }

    async onJenjangChange() {
        const wilayah = document.getElementById('filter-wilayah').value;
        const jenjang = document.getElementById('filter-jenjang').value;
        
        // Clear status select
        this.clearSelect('filter-status', 'Semua Status');
        
        // Load status options based on selected wilayah and jenjang
        await this.loadStatusOptions(wilayah, jenjang);
    }
    
    async fetchAPI(url) {
        this.toggleLoading(true);
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.error || 'API request failed');
            }
            
            return result.data;
        } catch (error) {
            console.error('Fetch API Error:', error);
            this.showToast('error', 'Error!', 'Gagal memuat data dari server.');
            return null;
        } finally {
            this.toggleLoading(false);
        }
    }

    showToast(type, title, message) {
        if (typeof iziToast !== 'undefined') {
            iziToast[type]({
                title: title,
                message: message,
                position: 'topRight'
            });
        } else {
            console.log(`${type}: ${title} - ${message}`);
        }
    }

    toggleLoading(isLoading) {
        const spinner = document.getElementById('filter-spinner');
        const applyBtn = document.getElementById('apply-filter');
        
        if (spinner) {
            spinner.classList.toggle('d-none', !isLoading);
        }
        
        if (applyBtn) {
            if (isLoading) {
                applyBtn.setAttribute('disabled', 'disabled');
            } else {
                applyBtn.removeAttribute('disabled');
            }
        }
    }

    async loadInitialFilterOptions() {
        try {
            // Load wilayah first (always available)
            const wilayahData = await this.fetchAPI('api/filter-data.php?type=wilayah');
            this.populateSelect('filter-wilayah', wilayahData);
            
            // Load all jenjang and status initially
            await this.loadJenjangOptions('');
            await this.loadStatusOptions('', '');
            
        } catch (error) {
            console.error('Error loading initial filter options:', error);
        }
    }

    async loadJenjangOptions(wilayah) {
        const params = new URLSearchParams({ type: 'jenjang' });
        if (wilayah) params.append('wilayah', wilayah);
        
        const data = await this.fetchAPI(`api/filter-data.php?${params}`);
        this.populateSelect('filter-jenjang', data);
    }

    async loadStatusOptions(wilayah, jenjang) {
        const params = new URLSearchParams({ type: 'status' });
        if (wilayah) params.append('wilayah', wilayah);
        if (jenjang) params.append('jenjang', jenjang);
        
        const data = await this.fetchAPI(`api/filter-data.php?${params}`);
        this.populateSelect('filter-status', data);
    }
    
    clearSelect(elementId, defaultText) {
        const select = document.getElementById(elementId);
        if (select) {
            select.innerHTML = `<option value="">${defaultText}</option>`;
        }
    }

    populateSelect(elementId, options) {
        const select = document.getElementById(elementId);
        if (!select || !Array.isArray(options)) return;
        
        // Keep the first option (default)
        const firstOption = select.querySelector('option[value=""]');
        select.innerHTML = '';
        if (firstOption) {
            select.appendChild(firstOption);
        }
        
        options.forEach(option => {
            const optionElement = document.createElement('option');
            optionElement.value = option;
            optionElement.textContent = option;
            select.appendChild(optionElement);
        });
    }
    
    async loadStatsCards() {
        const params = new URLSearchParams({ type: 'stats', ...this.currentFilters });
        const data = await this.fetchAPI(`api/chart-data.php?${params}`);
        
        if (data && data.length > 0) {
            const stats = data[0];
            this.updateStatCard('total-sekolah', stats.total_sekolah || 0);
            this.updateStatCard('total-ptk', stats.total_ptk || 0);
            this.updateStatCard('total-siswa', stats.total_siswa || 0);
            this.updateStatCard('total-rombel', stats.total_rombel || 0);
        } else {
            ['total-sekolah', 'total-ptk', 'total-siswa', 'total-rombel'].forEach(id => {
                this.updateStatCard(id, 0, true);
            });
        }
    }

    updateStatCard(elementId, value, isError = false) {
        const el = document.getElementById(elementId);
        if (el) {
            if (isError) {
                el.innerHTML = '<span class="text-danger">0</span>';
            } else {
                el.textContent = this.formatNumber(value);
            }
        }
    }
    
    initMap() {
        if (typeof L === 'undefined' || this.map) return;
        
        // Initialize map centered on Indonesia
        this.map = L.map('school-map').setView([-2.5, 118], 5);
        
        // Define tile layers
        this.mapTileLayers = {
            default: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }),
            dark: L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>, &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a> &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'
            }),
            satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
            })
        };
        
        // Add default layer
        this.mapTileLayers.default.addTo(this.map);
        this.updateThemeButton('default');
        
        // Add marker layer group
        this.mapLayers.addTo(this.map);
        
        // Load initial map data
        this.loadMapData();
    }

    changeMapTheme(theme) {
        if (!this.map || !this.mapTileLayers[theme]) return;
        
        // Remove current tile layer
        Object.values(this.mapTileLayers).forEach(layer => {
            if (this.map.hasLayer(layer)) {
                this.map.removeLayer(layer);
            }
        });
        
        // Add new tile layer
        this.mapTileLayers[theme].addTo(this.map);
        this.currentMapTheme = theme;
        this.updateThemeButton(theme);
    }

    updateThemeButton(activeTheme) {
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
    
    applyFilters() {
        this.currentFilters.wilayah = document.getElementById('filter-wilayah').value;
        this.currentFilters.jenjang = document.getElementById('filter-jenjang').value;
        this.currentFilters.status = document.getElementById('filter-status').value;
        
        this.showToast('info', 'Info', 'Menerapkan filter...');
        this.updateAllData();
    }
    
    updateAllData() {
        this.loadStatsCards();
        this.loadPTKData();
        this.loadPDData();
        this.loadMapData();
    }
    
    formatNumber(num) {
        if (num === null || num === undefined) return '0';
        return new Intl.NumberFormat('id-ID').format(num);
    }

    // Placeholder methods for chart loading (will be implemented in separate files)
    loadPTKData() {
        // Implemented in ptk-chart.js
    }

    loadPDData() {
        // Implemented in pd-chart.js
    }

    async loadMapData() {
        if (!this.map) return;

        // Show loading indicator
        const mapContainer = document.getElementById('school-map');
        const existingLoader = mapContainer.querySelector('.map-loading');
        if (existingLoader) {
            existingLoader.remove();
        }

        const loadingEl = document.createElement('div');
        loadingEl.className = 'map-loading';
        loadingEl.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>';
        mapContainer.appendChild(loadingEl);

        try {
            const params = new URLSearchParams({ ...this.currentFilters });
            const result = await this.fetchAPI(`api/map-data.php?${params}`);
            
            // Clear existing markers
            this.mapLayers.clearLayers();

            if (result && result.length > 0) {
                const markers = [];
                let bounds = L.latLngBounds();

                result.forEach(school => {
                    if (school.lat && school.lng && school.lat !== 0 && school.lng !== 0) {
                        const marker = this.createSchoolMarker(school);
                        markers.push(marker);
                        bounds.extend([school.lat, school.lng]);
                    }
                });
                
                // Add markers to layer group
                if (markers.length > 0) {
                    markers.forEach(marker => marker.addTo(this.mapLayers));
                    
                    // Fit map to show all markers with padding
                    if (bounds.isValid()) {
                        this.map.fitBounds(bounds, { padding: [20, 20] });
                    }
                    
                    this.showToast('success', 'Sukses', `Menampilkan ${markers.length} sekolah di peta.`);
                } else {
                    this.showToast('warning', 'Peringatan', 'Tidak ada koordinat sekolah yang valid untuk ditampilkan.');
                }
            } else {
                this.showToast('warning', 'Peringatan', 'Tidak ada data sekolah dengan filter yang dipilih.');
            }
        } catch (error) {
            console.error('Error loading map data:', error);
            this.showToast('error', 'Error', 'Gagal memuat data peta.');
        } finally {
            // Remove loading indicator
            const loader = mapContainer.querySelector('.map-loading');
            if (loader) {
                loader.remove();
            }
        }
    }

    createSchoolMarker(school) {
        const markerColor = this.getMarkerColor(school.jenjang);
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
                    <p class="mb-1"><strong>Siswa:</strong> ${this.formatNumber(school.siswa)} | <strong>Rombel:</strong> ${school.rombel}</p>
                    <p class="mb-1"><strong>PTK:</strong> ${this.formatNumber(school.ptk)} orang</p>
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

    getMarkerColor(jenjang) {
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
}

// Initialize dashboard
window.analyticsDashboard = new AnalyticsDashboard();