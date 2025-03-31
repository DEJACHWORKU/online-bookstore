// Hamburger menu toggle
const hamburger = document.querySelector('.hamburger');
const navMenu = document.querySelector('.nav-menu');

hamburger.addEventListener('click', () => {
    navMenu.classList.toggle('active');
});

// Theme change functionality
const dropdownItems = document.querySelectorAll('.dropdown a[data-theme]');
const body = document.body;

let currentTheme = localStorage.getItem('theme') || 'default';
let currentIntensity = parseInt(localStorage.getItem('intensity')) || 0;

function applyTheme(theme, intensity) {
    // Clear all classes from body
    body.className = '';
    // Apply new theme and intensity
    body.classList.add(theme);
    if (intensity > 0) {
        body.classList.add(`intensity-${intensity}`);
    }
    // Update global variables
    currentTheme = theme;
    currentIntensity = intensity;
    // Persist to localStorage
    localStorage.setItem('theme', theme);
    localStorage.setItem('intensity', intensity);
}

// Load saved theme on page load
document.addEventListener('DOMContentLoaded', () => {
    applyTheme(currentTheme, currentIntensity);
});

// Theme selection
dropdownItems.forEach(item => {
    item.addEventListener('click', (e) => {
        e.preventDefault();
        const selectedTheme = item.getAttribute('data-theme');
        applyTheme(selectedTheme, 0); // Reset intensity on theme change
    });
});

// Intensity controls
document.querySelectorAll('.icon-container').forEach(container => {
    const minusBtn = container.querySelector('.minus-circle');
    const plusBtn = container.querySelector('.plus-circle');

    minusBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation(); // Prevent dropdown click from firing
        if (currentIntensity > 0) {
            applyTheme(currentTheme, currentIntensity - 1);
        }
    });

    plusBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation(); // Prevent dropdown click from firing
        if (currentIntensity < 10) {
            applyTheme(currentTheme, currentIntensity + 1);
        }
    });
});

// Scroll functionality
const scrollTop = document.querySelector('#scroll-top');
scrollTop.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

const scrollDown = document.querySelector('#scroll-down');
scrollDown.addEventListener('click', () => {
    window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
});