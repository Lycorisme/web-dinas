// Dashboard JavaScript - dengan IIFE untuk menghindari konflik
(function($) {
  'use strict';
  
  $(document).ready(function() {
    // Fungsi untuk format angka Indonesia
    function formatNumber(num) {
      return new Intl.NumberFormat('id-ID').format(num);
    }
    
    // Counter animation untuk statistics
    const counters = $('.stats-number');
    
    counters.each(function() {
      const counter = $(this);
      const target = parseInt(counter.attr('data-count')) || 0;
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
          counter.text(formatNumber(displayValue));
          frame++;
          requestAnimationFrame(updateCounter);
        } else {
          // Pastikan nilai akhir benar
          counter.text(formatNumber(target));
        }
      }
      
      // Mulai animasi setelah delay untuk efek bertahap
      const delay = counters.index(counter) * 200;
      setTimeout(() => {
        updateCounter();
      }, delay);
    });
    
    // Jenjang Pendidikan Chart
    // Get chart data from DOM element
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
              data: jenjangData.map(item => item.jumlah),
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
        
        // Store chart reference globally for resize handling
        window.jenjangChart = jenjangChart;
      }
    }
    
    // Dropdown functionality
    const dropdownTriggers = [
      { 
        trigger: $('#rekapDropdown'), 
        menu: $('#rekapMenu'),
        container: null
      },
      { 
        trigger: $('#wilayahDropdown'), 
        menu: $('#wilayahMenu'),
        container: null
      }
    ];
    
    // Inisialisasi dropdown containers
    dropdownTriggers.forEach(dropdown => {
      if (dropdown.trigger.length && dropdown.menu.length) {
        dropdown.container = dropdown.trigger.closest('.dropdown-container');
      }
    });
    
    // Event listeners untuk dropdown
    dropdownTriggers.forEach(({ trigger, menu, container }) => {
      if (trigger.length && menu.length && container.length) {
        trigger.on('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          
          const isActive = container.hasClass('active');
          
          // Close all dropdowns dengan animasi
          dropdownTriggers.forEach(({ container: otherContainer }) => {
            if (otherContainer && otherContainer.length && otherContainer !== container) {
              otherContainer.removeClass('active');
            }
          });
          
          // Toggle current dropdown dengan delay untuk animasi smooth
          if (!isActive) {
            container.addClass('active');
            // Trigger reflow untuk animasi CSS
            menu[0].offsetHeight;
          } else {
            container.removeClass('active');
          }
        });
      }
    });
    
    // Close dropdowns saat klik di luar
    $(document).on('click', function(e) {
      // Cek apakah klik di dalam dropdown
      const clickedInsideDropdown = $(e.target).closest('.dropdown-container');
      
      if (!clickedInsideDropdown.length) {
        dropdownTriggers.forEach(({ container }) => {
          if (container && container.length) {
            container.removeClass('active');
          }
        });
      }
    });
    
    // Prevent dropdown dari menutup saat klik di dalam menu
    $('.dropdown-menu-custom').on('click', function(e) {
      e.stopPropagation();
    });
    
    // Hover effects untuk cards
    $('.stats-card').each(function() {
      const card = $(this);
      card.on('mouseenter', function() {
        card.css('transform', 'translateY(-5px)');
      });
      
      card.on('mouseleave', function() {
        card.css('transform', 'translateY(0)');
      });
    });
    
    // Parallax effect untuk floating cards di hero section
    const floatingCards = $('.floating-card');
    let ticking = false;
    
    function updateFloatingCards() {
      const scrolled = window.pageYOffset;
      const parallax = scrolled * 0.5;
      
      floatingCards.each(function(index) {
        const card = $(this);
        const speed = (index + 1) * 0.2;
        card.css('transform', `translateY(${parallax * speed}px)`);
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
      $(window).on('scroll', requestTick);
    }
    
    // Smooth scroll untuk internal links
    $('a[href^="#"]').on('click', function(e) {
      e.preventDefault();
      const target = $(this.getAttribute('href'));
      if (target.length) {
        $('html, body').animate({
          scrollTop: target.offset().top
        }, 500);
      }
    });
    
    // Loading state untuk stats cards
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          $(entry.target).addClass('animate-in');
        }
      });
    }, {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    });
    
    // Observe stats cards untuk animasi on scroll
    $('.stats-card').each(function() {
      observer.observe(this);
    });
    
    // Keyboard navigation untuk dropdowns
    $(document).on('keydown', function(e) {
      if (e.key === 'Escape') {
        // Close all dropdowns saat tekan Escape
        dropdownTriggers.forEach(({ container }) => {
          if (container && container.length) {
            container.removeClass('active');
          }
        });
      }
    });
    
    // Progressive enhancement untuk touch devices
    if ('ontouchstart' in window) {
      $('body').addClass('touch-device');
      
      // Disable hover effects pada touch devices
      $('.stats-card, .quick-action-item, .dropdown-item-custom').each(function() {
        const element = $(this);
        element.on('touchstart', function() {
          element.addClass('touch-active');
        });
        
        element.on('touchend', function() {
          setTimeout(() => {
            element.removeClass('touch-active');
          }, 300);
        });
      });
    }
    
    // Auto-refresh data (optional)
    function refreshStats() {
      // Implementasi refresh data via AJAX bisa ditambahkan di sini
      console.log('Stats refreshed at:', new Date().toLocaleTimeString());
    }
    
    // Error handling untuk chart
    $(window).on('error', function(e) {
      if (e.target && e.target.id === 'jenjangChart') {
        console.warn('Chart failed to load, hiding chart container');
        const chartContainer = $(e.target).closest('.jenjang-visual');
        if (chartContainer.length) {
          chartContainer.hide();
        }
      }
    });
    
    // Resize handler untuk responsive chart
    let resizeTimer;
    $(window).on('resize', function() {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function() {
        // Update chart dimensions jika ada
        if (typeof window.jenjangChart !== 'undefined') {
          window.jenjangChart.resize();
        }
      }, 250);
    });
    
    // Console log untuk debugging
    console.log('Dashboard initialized successfully');
  });
})(jQuery);