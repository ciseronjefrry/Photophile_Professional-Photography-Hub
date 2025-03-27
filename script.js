document.addEventListener('DOMContentLoaded', function() {
    // Add smooth scroll behavior
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Add animation to new messages
    const messageCards = document.querySelectorAll('.message-card, .admin-message-card');
    messageCards.forEach(card => {
        card.addEventListener('mouseover', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseout', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Animate submit buttons
    const buttons = document.querySelectorAll('.btn-send, .btn-reply');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            this.classList.add('animate__animated', 'animate__pulse');
        });
    });
});
