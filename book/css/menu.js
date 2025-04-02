document.addEventListener("DOMContentLoaded", function() {
    // Change the book icon to Font Awesome
    const icon = document.querySelector(".icon");
    if (icon) {
        icon.innerHTML = '<i class="fas fa-book-open"></i>';
        icon.classList.add("fa-icon");
    }

    const hamburger = document.querySelector(".hamburger");
    const navMenu = document.querySelector(".nav-menu");
    const navLinks = document.querySelectorAll('.nav-menu a[data-page]');
    const contentFrame = document.getElementById('contentFrame');
    const body = document.body;

    // Hamburger toggle
    hamburger.addEventListener("click", function(e) {
        e.stopPropagation();
        this.classList.toggle("active");
        navMenu.classList.toggle("active");
        body.classList.toggle("menu-open");
    });

    // Navigation with up-to-down animation
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const pageUrl = this.getAttribute('data-page');
            
            // Reset iframe animation
            contentFrame.style.opacity = '0';
            contentFrame.style.transform = 'translateY(-20px)';
            
            // Close menu if open
            if (navMenu.classList.contains('active')) {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
                body.classList.remove('menu-open');
            }
            
            // Load new content with animation
            setTimeout(() => {
                contentFrame.src = pageUrl;
                contentFrame.onload = function() {
                    contentFrame.style.opacity = '1';
                    contentFrame.style.transform = 'translateY(0)';
                };
            }, 300);
            
            // Update active link
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.nav-menu') && 
            !e.target.closest('.hamburger') && 
            navMenu.classList.contains('active')) {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
            body.classList.remove('menu-open');
        }
    });

    // Initial iframe load
    contentFrame.onload = function() {
        contentFrame.classList.add('loaded');
    };
});