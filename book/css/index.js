function changeTheme(theme, event) {
    event.stopPropagation();
    const currentPower = parseInt(localStorage.getItem('themePower')) || 2;
    document.body.className = theme + ' power-' + currentPower;
    localStorage.setItem('selectedTheme', theme);
}

function adjustPower(theme, delta, event) {
    event.stopPropagation();
    const currentTheme = document.body.className.split(' ')[0] || 'theme-default';
    if (currentTheme !== theme) return;
    let currentPower = parseInt(localStorage.getItem('themePower')) || 2;
    currentPower = Math.max(1, Math.min(10, currentPower + delta));
    document.body.className = theme + ' power-' + currentPower;
    localStorage.setItem('themePower', currentPower);
}

window.onload = function() {
    const savedTheme = localStorage.getItem('selectedTheme') || 'theme-default';
    const savedPower = localStorage.getItem('themePower') || '2';
    document.body.className = savedTheme + ' power-' + savedPower;
}

document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.querySelector('#settingsDropdown');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    dropdown.addEventListener('click', function(event) {
        event.stopPropagation();
        dropdownMenu.classList.add('show');
    });
    document.addEventListener('click', function(event) {
        if (!dropdownMenu.contains(event.target) && !dropdown.contains(event.target)) {
            dropdownMenu.classList.remove('show');
        }
    });
});