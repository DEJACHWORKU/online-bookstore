
    document.addEventListener('DOMContentLoaded', function() {
        const menuIcon = document.getElementById('menu-icon');
        const navbar = document.querySelector('.navbar');
        const dropdowns = document.querySelectorAll('.dropdown');
        const themeSwitcher = document.querySelector('.theme-switcher');
        const themeOptions = document.getElementById('theme-options');
        const settingsToggle = document.getElementById('settings-toggle');
        
        menuIcon.addEventListener('click', function() {
            navbar.classList.toggle('active');
            menuIcon.classList.toggle('bx-x');
        });
        
        dropdowns.forEach(dropdown => {
            const toggle = dropdown.querySelector('.dropdown-toggle');
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                dropdown.classList.toggle('active');
            });
        });
        
        settingsToggle.addEventListener('click', function(e) {
            e.preventDefault();
            themeOptions.classList.toggle('active');
        });
        
        document.querySelectorAll('.theme-option').forEach(option => {
            option.addEventListener('click', function() {
                const theme = this.getAttribute('data-theme');
                document.body.className = theme;
                localStorage.setItem('bookstoreTheme', theme);
                themeOptions.classList.remove('active');
            });
        });
        
        const savedTheme = localStorage.getItem('bookstoreTheme');
        if (savedTheme) {
            document.body.className = savedTheme;
        }
        
        document.addEventListener('click', function(e) {
            if (!themeSwitcher.contains(e.target) && themeOptions.classList.contains('active')) {
                themeOptions.classList.remove('active');
            }
            dropdowns.forEach(dropdown => {
                if (!dropdown.contains(e.target)) {
                    dropdown.classList.remove('active');
                }
            });
            if (!navbar.contains(e.target) && !menuIcon.contains(e.target) && navbar.classList.contains('active')) {
                navbar.classList.remove('active');
                menuIcon.classList.remove('bx-x');
            }
        });
        
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
        
        animateCounters();
        
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
                        if (link.getAttribute('href')?.includes(id)) {
                            link.classList.add('active');
                        }
                    });
                }
            });
            header.classList.toggle('sticky', window.scrollY > 100);
        });
        
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
        
        function updateNotificationCount() {
            fetch('?notification_count=1')
                .then(response => response.text())
                .then(count => {
                    const badge = document.getElementById('notification-badge');
                    if (count > 0) {
                        badge.style.display = 'block';
                        badge.textContent = count;
                    } else {
                        badge.style.display = 'none';
                    }
                });
        }
        
        setInterval(updateNotificationCount, 1000);
    });
