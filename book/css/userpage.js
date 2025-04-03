document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    const overlay = document.querySelector('.overlay');
    const scrollDown = document.querySelector('.scroll-down');
    const scrollTopBtn = document.getElementById('scroll-top');
    const logoutBtn = document.querySelector('.logout');

    // Toggle mobile menu
    function toggleMenu() {
        navMenu.classList.toggle('active');
        overlay.classList.toggle('active');
        document.body.classList.toggle('menu-open');
    }

    hamburger.addEventListener('click', toggleMenu);
    overlay.addEventListener('click', toggleMenu);

    // Close menu when clicking on nav links
    document.querySelectorAll('.nav-menu a').forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                toggleMenu();
            }
        });
    });

    // Logout button
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
            window.location.href = 'logout.php';
        });
    }

    // Scroll down button
    if (scrollDown) {
        scrollDown.addEventListener('click', function() {
            window.scrollBy({ top: window.innerHeight, behavior: 'smooth' });
        });
    }

    // Scroll to top button
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollTopBtn.classList.add('active');
        } else {
            scrollTopBtn.classList.remove('active');
        }
    });

    scrollTopBtn.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Search functionality
    const searchInputs = document.querySelectorAll('.search-input');
    const searchIcons = document.querySelectorAll('.search-icon');
    
    function submitSearch() {
        const form = document.createElement('form');
        form.method = 'GET';
        form.style.display = 'none';
        
        searchInputs.forEach(input => {
            if (input.value.trim()) {
                const hiddenField = document.createElement('input');
                hiddenField.type = 'hidden';
                hiddenField.name = input.name;
                hiddenField.value = input.value.trim();
                form.appendChild(hiddenField);
            }
        });
        
        document.body.appendChild(form);
        form.submit();
    }

    searchIcons.forEach(icon => icon.addEventListener('click', submitSearch));
    searchInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') submitSearch();
        });
    });

    // Footer animation adjustment
    function adjustFooterAnimation() {
        const footerText = document.querySelector('.footer-text');
        const duration = Math.max(20, footerText.scrollWidth / 100);
        footerText.style.animation = 'none';
        void footerText.offsetWidth;
        footerText.style.animation = `slideLeft ${duration}s linear infinite`;
    }

    window.addEventListener('load', adjustFooterAnimation);
    window.addEventListener('resize', adjustFooterAnimation);
});