document.addEventListener('DOMContentLoaded', function() {
    // Animation for cards
    const cards = document.querySelectorAll('.stat-card, .content-card, .info-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${0.6 + (index * 0.2)}s`;
    });
    
    // View detail function
    window.viewDetail = function(nama, kecamatan, sekolah) {
        iziToast.info({
            title: nama,
            message: `Kecamatan: ${kecamatan} | Sekolah: ${sekolah}`,
            position: 'topRight',
            timeout: 5000
        });
    };
    
    // Add ripple effect to buttons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
});