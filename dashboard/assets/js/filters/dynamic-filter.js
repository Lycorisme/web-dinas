// dashboard/assets/js/filters/dynamic-filter.js
// Enhanced filter functionality is now integrated into analytics-main.js
// This file provides additional filter utilities if needed in the future

class FilterUtilities {
    static validateFilterCombination(filters) {
        // Validate filter combinations
        const { wilayah, jenjang, status } = filters;
        
        if (!wilayah && (jenjang || status)) {
            console.warn('Jenjang or Status filter selected without Wilayah filter');
        }
        
        return true;
    }
    
    static getFilterSummary(filters) {
        const summary = [];
        
        if (filters.wilayah) {
            summary.push(`Wilayah: ${filters.wilayah}`);
        }
        
        if (filters.jenjang) {
            summary.push(`Jenjang: ${filters.jenjang}`);
        }
        
        if (filters.status) {
            summary.push(`Status: ${filters.status}`);
        }
        
        return summary.length > 0 ? summary.join(' | ') : 'Semua Data';
    }
    
    static clearAllFilters() {
        const selects = ['filter-wilayah', 'filter-jenjang', 'filter-status'];
        selects.forEach(id => {
            const select = document.getElementById(id);
            if (select) {
                select.selectedIndex = 0;
            }
        });
    }
}

// Make utilities available globally
window.FilterUtilities = FilterUtilities;