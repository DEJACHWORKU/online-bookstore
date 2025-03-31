document.addEventListener('DOMContentLoaded', function() {
    let currentTheme = localStorage.getItem('selectedTheme') || 'default';
    let currentIntensity = parseInt(localStorage.getItem('selectedIntensity')) || 0;
    const MAX_INTENSITY = 10;

    updateThemeClass();

    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    const themeToggle = document.querySelector('.theme-toggle');
    const dropdown = document.querySelector('.dropdown');
    const scrollDown = document.getElementById('scroll-down');
    const scrollTop = document.getElementById('scroll-top');
    const header = document.querySelector('header');

    function updateThemeClass() {
        document.body.className = currentTheme;
        if (currentIntensity > 0 && (currentTheme === 'light-reader' || currentTheme === 'sepia-reader')) {
            document.body.classList.add(`intensity-${currentIntensity}`);
        }
    }

    function saveTheme() {
        localStorage.setItem('selectedTheme', currentTheme);
        localStorage.setItem('selectedIntensity', currentIntensity);
    }

    hamburger.addEventListener('click', function(e) {
        e.stopPropagation();
        navMenu.classList.toggle('active');
        dropdown.style.display = 'none';
        
        if (navMenu.classList.contains('active')) {
            hamburger.innerHTML = '<i class="fas fa-times"></i>';
            const headerHeight = header.offsetHeight;
            navMenu.style.top = `${headerHeight}px`;
            navMenu.style.height = `calc(100vh - ${headerHeight}px)`;
        } else {
            hamburger.innerHTML = '<i class="fas fa-bars"></i>';
        }
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.nav-menu') && !e.target.closest('.hamburger')) {
            navMenu.classList.remove('active');
            hamburger.innerHTML = '<i class="fas fa-bars"></i>';
        }
    });

    document.querySelectorAll('.dropdown a[data-theme]').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            currentTheme = this.getAttribute('data-theme');
            currentIntensity = 0;
            updateThemeClass();
            saveTheme();
        });

        const minusBtn = item.querySelector('.minus-circle');
        const plusBtn = item.querySelector('.plus-circle');

        if (minusBtn) {
            minusBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                if (currentTheme === item.getAttribute('data-theme')) {
                    currentIntensity = Math.max(0, currentIntensity - 1);
                    updateThemeClass();
                    saveTheme();
                }
            });
        }

        if (plusBtn) {
            plusBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                if (currentTheme === item.getAttribute('data-theme')) {
                    currentIntensity = Math.min(MAX_INTENSITY, currentIntensity + 1);
                    updateThemeClass();
                    saveTheme();
                }
            });
        }
    });

    themeToggle.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    });

    scrollDown.addEventListener('click', function() {
        document.querySelector('footer').scrollIntoView({ behavior: 'smooth' });
        navMenu.classList.remove('active');
        hamburger.innerHTML = '<i class="fas fa-bars"></i>';
    });

    scrollTop.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        navMenu.classList.remove('active');
        hamburger.innerHTML = '<i class="fas fa-bars"></i>';
    });

    window.addEventListener('scroll', function() {
        scrollTop.style.opacity = window.pageYOffset > 300 ? '0.9' : '0.6';
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            navMenu.classList.remove('active');
            hamburger.innerHTML = '<i class="fas fa-bars"></i>';
            navMenu.style.top = '';
            navMenu.style.height = '';
        } else if (navMenu.classList.contains('active')) {
            const headerHeight = header.offsetHeight;
            navMenu.style.top = `${headerHeight}px`;
            navMenu.style.height = `calc(100vh - ${headerHeight}px)`;
        }
    });

    scrollTop.style.display = 'flex';
    scrollTop.style.opacity = '0.6';
});