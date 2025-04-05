document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    const overlay = document.querySelector('.overlay');
    const scrollTopBtn = document.getElementById('scroll-top');
    const logoutBtn = document.querySelector('.logout');
    const searchInput = document.querySelector('.search-input');
    const searchIcon = document.querySelector('.search-icon');

    function toggleMenu() {
        navMenu.classList.toggle('active');
        overlay.classList.toggle('active');
        document.body.classList.toggle('menu-open');
    }

    hamburger.addEventListener('click', toggleMenu);
    overlay.addEventListener('click', toggleMenu);

    document.querySelectorAll('.nav-menu a').forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                toggleMenu();
            }
        });
    });

    if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
            window.location.href = 'logout.php';
        });
    }

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

    function submitSearch() {
        const form = document.createElement('form');
        form.method = 'GET';
        form.style.display = 'none';
        
        if (searchInput.value.trim()) {
            const hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = 'search';
            hiddenField.value = searchInput.value.trim();
            form.appendChild(hiddenField);
        }
        
        document.body.appendChild(form);
        form.submit();
    }

    searchIcon.addEventListener('click', submitSearch);
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') submitSearch();
    });

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