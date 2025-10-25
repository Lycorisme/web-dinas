// assets/js/dashboard.js
document.addEventListener('DOMContentLoaded', function() {
    // Counter animation for statistics dengan perbaikan
    const counters = document.querySelectorAll('.stats-number');
    
    // Fungsi untuk format angka Indonesia
    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }
    
    // Animasi counter yang diperbaiki
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-count')) || 0;
        const duration = 2000; // 2 detik
        const frameRate = 60;
        const totalFrames = (duration / 1000) * frameRate;
        const increment = target / totalFrames;
        
        let currentCount = 0;
        let frame = 0;
        
        function updateCounter() {
            if (frame < totalFrames) {
                currentCount += increment;
                const displayValue = Math.floor(currentCount);
                counter.innerText = formatNumber(displayValue);
                frame++;
                requestAnimationFrame(updateCounter);
            } else {
                // Pastikan nilai akhir benar
                counter.innerText = formatNumber(target);
            }
        }
        
        // Mulai animasi setelah delay untuk efek bertahap
        const delay = Array.from(counters).indexOf(counter) * 200;
        setTimeout(() => {
            updateCounter();
        }, delay);
    });
    
    // Jenjang Pendidikan Chart dengan perbaikan
    <?php if (!empty($jenjang_data)): ?>
      const ctx = document.getElementById('jenjangChart');
      if (ctx) {
        const chartCtx = ctx.getContext('2d');
        const jenjangChart = new Chart(chartCtx, {
          type: 'doughnut',
          data: {
            labels: <?= json_encode(array_column($jenjang_data, 'jenjang_pendidikan')) ?>,
            datasets: [{
              data: <?= json_encode(array_column($jenjang_data, 'jumlah')) ?>,
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
                    return `${label}: ${formatNumber(value)} sekolah (${percentage}%)`;
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
    <?php endif; ?>
    
    // Dropdown functionality dengan perbaikan elegant
    const dropdownTriggers = [
        { 
          trigger: document.getElementById('rekapDropdown'), 
          menu: document.getElementById('rekapMenu'),
          container: null
        },
        { 
          trigger: document.getElementById('wilayahDropdown'), 
          menu: document.getElementById('wilayahMenu'),
          container: null
        }
    ];
    
    // Inisialisasi dropdown containers
    dropdownTriggers.forEach(dropdown => {
        if (dropdown.trigger && dropdown.menu) {
            dropdown.container = dropdown.trigger.closest('.dropdown-container');
        }
    });
    
    // Event listeners untuk dropdown
    dropdownTriggers.forEach(({ trigger, menu, container }) => {
        if (trigger && menu && container) {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const isActive = container.classList.contains('active');
                
                // Close all dropdowns dengan animasi
                dropdownTriggers.forEach(({ container: otherContainer }) => {
                    if (otherContainer && otherContainer !== container) {
                        otherContainer.classList.remove('active');
                    }
                });
                
                // Toggle current dropdown dengan delay untuk animasi smooth
                if (!isActive) {
                    container.classList.add('active');
                    // Trigger reflow untuk animasi CSS
                    menu.offsetHeight;
                } else {
                    container.classList.remove('active');
                }
            });
        }
    });
    
    // Close dropdowns saat klik di luar
    document.addEventListener('click', function(e) {
        // Cek apakah klik di dalam dropdown
        const clickedInsideDropdown = e.target.closest('.dropdown-container');
        
        if (!clickedInsideDropdown) {
            dropdownTriggers.forEach(({ container }) => {
                if (container) {
                    container.classList.remove('active');
                }
            });
        }
    });
    
    // Prevent dropdown dari menutup saat klik di dalam menu
    document.querySelectorAll('.dropdown-menu-custom').forEach(menu => {
        menu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
    
    // Hover effects untuk cards
    const statsCards = document.querySelectorAll('.stats-card');
    statsCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Parallax effect untuk floating cards di hero section
    const floatingCards = document.querySelectorAll('.floating-card');
    let ticking = false;
    
    function updateFloatingCards() {
        const scrolled = window.pageYOffset;
        const parallax = scrolled * 0.5;
        
        floatingCards.forEach((card, index) => {
            const speed = (index + 1) * 0.2;
            card.style.transform = `translateY(${parallax * speed}px)`;
        });
        
        ticking = false;
    }
    
    function requestTick() {
        if (!ticking) {
            requestAnimationFrame(updateFloatingCards);
            ticking = true;
        }
    }
    
    // Hanya jalankan parallax di desktop
    if (window.innerWidth > 768) {
        window.addEventListener('scroll', requestTick);
    }
    
    // Smooth scroll untuk internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Loading state untuk stats cards
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    // Observe stats cards untuk animasi on scroll
    statsCards.forEach(card => {
        observer.observe(card);
    });
    
    // Keyboard navigation untuk dropdowns
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            // Close all dropdowns saat tekan Escape
            dropdownTriggers.forEach(({ container }) => {
                if (container) {
                    container.classList.remove('active');
                }
            });
        }
    });
    
    // Progressive enhancement untuk touch devices
    if ('ontouchstart' in window) {
        document.body.classList.add('touch-device');
        
        // Disable hover effects pada touch devices
        const hoverElements = document.querySelectorAll('.stats-card, .quick-action-item, .dropdown-item-custom');
        hoverElements.forEach(element => {
            element.addEventListener('touchstart', function() {
                this.classList.add('touch-active');
            });
            
            element.addEventListener('touchend', function() {
                setTimeout(() => {
                    this.classList.remove('touch-active');
                }, 300);
            });
        });
    }
    
    // Auto-refresh data (optional)
    function refreshStats() {
        // Implementasi refresh data via AJAX bisa ditambahkan di sini
        console.log('Stats refreshed at:', new Date().toLocaleTimeString());
    }
    
    // Refresh setiap 5 menit (optional)
    // setInterval(refreshStats, 5 * 60 * 1000);
    
    // Error handling untuk chart
    window.addEventListener('error', function(e) {
        if (e.target && e.target.id === 'jenjangChart') {
            console.warn('Chart failed to load, hiding chart container');
            const chartContainer = e.target.closest('.jenjang-visual');
            if (chartContainer) {
                chartContainer.style.display = 'none';
            }
        }
    });
    
    // Resize handler untuk responsive chart
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            // Update chart dimensions jika ada
            if (typeof jenjangChart !== 'undefined') {
                jenjangChart.resize();
            }
        }, 250);
    });
    
    // Console log untuk debugging
    console.log('Dashboard initialized successfully');
    console.log('Stats loaded:', {
        sekolah: <?= $stats['total_sekolah'] ?>,
        ptk: <?= $stats['total_ptk'] ?>,
        siswa: <?= $stats['total_siswa'] ?>,
        rombel: <?= $stats['total_rombel'] ?>
    });
});