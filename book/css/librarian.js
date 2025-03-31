document.addEventListener("DOMContentLoaded", function () {
    const hamburger = document.querySelector(".hamburger");
    const navMenu = document.querySelector(".nav-menu");

    hamburger.addEventListener("click", function () {
        navMenu.classList.toggle("active");
        hamburger.classList.toggle("active");
    });
});
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.nav-menu a[data-page]');
    const contentFrame = document.getElementById('contentFrame');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const pageUrl = this.getAttribute('data-page');
            
            // Hide current frame
            contentFrame.classList.add('hidden');
            
            // Create new iframe for animation
            const newFrame = document.createElement('iframe');
            newFrame.src = pageUrl;
            newFrame.classList.add('slide-animation');
            newFrame.style.width = '100%';
            newFrame.style.minHeight = '500px';
            newFrame.style.border = 'none';
            newFrame.style.borderRadius = '8px';
            newFrame.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
            
            // Replace old iframe with new animated one
            const iframeContainer = document.querySelector('.iframe-container');
            iframeContainer.innerHTML = '';
            iframeContainer.appendChild(newFrame);
            
            // Remove hidden class after a small delay to allow DOM update
            setTimeout(() => {
                newFrame.classList.remove('hidden');
            }, 10);
            
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });
});