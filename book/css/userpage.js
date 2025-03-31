document.addEventListener('DOMContentLoaded', function() {
    let currentTheme = localStorage.getItem('selectedTheme') || 'default';
    let currentIntensity = parseInt(localStorage.getItem('selectedIntensity')) || 0;
    const MAX_INTENSITY = 10;

    updateThemeClass();

    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');

    hamburger.addEventListener('click', function() {
        navMenu.classList.toggle('active');
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.hamburger') && !e.target.closest('.nav-menu')) {
            navMenu.classList.remove('active');
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

    const themeToggle = document.querySelector('.theme-toggle');
    const dropdown = document.querySelector('.dropdown');

    themeToggle.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropdown.style.display = dropdown.style.display === 'none' || dropdown.style.display === '' ? 'block' : 'none';
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.theme-selector')) {
            dropdown.style.display = 'none';
        }
    });

    document.getElementById('scroll-down').addEventListener('click', function() {
        document.querySelector('footer').scrollIntoView({ behavior: 'smooth' });
        navMenu.classList.remove('active');
    });

    document.getElementById('scroll-top').addEventListener('click', function() {
        document.getElementById('header').scrollIntoView({ behavior: 'smooth' });
        navMenu.classList.remove('active');
    });

    function updateThemeClass() {
        const intensityClass = currentIntensity > 0 ? ` intensity-${currentIntensity}` : '';
        document.body.className = `${currentTheme}${intensityClass}`;
    }

    function saveTheme() {
        localStorage.setItem('selectedTheme', currentTheme);
        localStorage.setItem('selectedIntensity', currentIntensity);
    }
});