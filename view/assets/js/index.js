document.addEventListener('DOMContentLoaded', function() {
    // Animation for hero buttons
    const heroButtons = document.querySelectorAll('.hero-button');
    heroButtons.forEach((button, index) => {
        button.style.animationDelay = `${0.6 + (index * 0.1)}s`;
    });
    
    // Animation for login button
    const loginButton = document.querySelector('.login-button');
    if (loginButton) {
        loginButton.style.animationDelay = '1s';
    }
    
    // Add ripple effect to buttons
    const buttons = document.querySelectorAll('.hero-button, .login-button');
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