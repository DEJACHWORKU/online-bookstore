document.addEventListener('DOMContentLoaded', function() {
    // 1. Theme Switcher with Local Storage
    const themeSwitcher = document.querySelector('.theme-switcher');
    const themeOptions = document.getElementById('theme-options');
    const settingsToggle = document.getElementById('settings-toggle');
    
    settingsToggle.addEventListener('click', function(e) {
        e.preventDefault();
        themeOptions.style.display = themeOptions.style.display === 'block' ? 'none' : 'block';
    });
    
    document.querySelectorAll('.theme-option').forEach(option => {
        option.addEventListener('click', function() {
            const theme = this.getAttribute('data-theme');
            document.body.className = theme;
            localStorage.setItem('bookstoreTheme', theme);
            themeOptions.style.display = 'none'; // Hide after selection
        });
    });
    
    // Load saved theme
    const savedTheme = localStorage.getItem('bookstoreTheme');
    if (savedTheme) {
        document.body.className = savedTheme;
    }

    // 2. Animated Counters
    const animateCounters = () => {
        const counters = document.querySelectorAll('.counter');
        const speed = 200;
        
        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-target');
                const count = +counter.innerText;
                const increment = target / speed;
                
                if (count < target) {
                    counter.innerText = Math.ceil(count + increment);
                    setTimeout(updateCount, 1);
                } else {
                    counter.innerText = target;
                }
            };
            
            updateCount();
        });
    };
    
    // 4. Responsive Menu Toggle
    const menuIcon = document.getElementById('menu-icon');
    const navbar = document.querySelector('.navbar');
    
    menuIcon.addEventListener('click', function() {
        navbar.classList.toggle('active');
        menuIcon.classList.toggle('bx-x');
    });

    // 5. Dropdown Menu (Login)
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            dropdown.classList.toggle('active');
        });
    });

    // 6. Hide Dropdowns and Theme Options on Outside Click
    document.addEventListener('click', function(e) {
        // Hide theme options if click is outside theme-switcher
        if (!themeSwitcher.contains(e.target) && themeOptions.style.display === 'block') {
            themeOptions.style.display = 'none';
        }
        
        // Hide login dropdown if click is outside any dropdown
        dropdowns.forEach(dropdown => {
            if (!dropdown.contains(e.target) && dropdown.classList.contains('active')) {
                dropdown.classList.remove('active');
            }
        });
    });

    // 7. Active Section Highlighting
    window.addEventListener('scroll', function() {
        const sections = document.querySelectorAll('section');
        const navLinks = document.querySelectorAll('.navbar a');
        const header = document.querySelector('header');
        
        sections.forEach(sec => {
            const top = window.scrollY;
            const offset = sec.offsetTop - 150;
            const height = sec.offsetHeight;
            const id = sec.getAttribute('id');
            
            if (top >= offset && top < offset + height) {
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href').includes(id)) {
                        link.classList.add('active');
                    }
                });
            }
        });
        
        header.classList.toggle('sticky', window.scrollY > 100);
    });

    // 8. Scroll Animations
    ScrollReveal().reveal('.home-content, .heading', { 
        origin: 'top',
        distance: '80px',
        duration: 2000,
        delay: 200
    });
    
    ScrollReveal().reveal('.home-img, .services-container, .portfolio-container, .stats-container', { 
        origin: 'bottom',
        distance: '80px',
        duration: 2000,
        delay: 200
    });

    // Initialize counters when page loads
    animateCounters();
});