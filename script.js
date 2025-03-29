let menuIcon=document.querySelector('#menu-icon');
let navbar=document.querySelector('.navbar');

menuIcon.onclick=()=>{
    menuIcon.classList.toggle('bx-x');
    navbar.classList.toggle('active');
};


let sections=document.querySelectorAll('section');
let navLinks=document.querySelectorAll('header nav a');

window.onscroll = () => {
    sections.forEach(sec => {
        let top = window.scrollY;
        let offset = sec.offsetTop - 150;
        let height = sec.offsetHeight;
        let id = sec.getAttribute('id');

        if(top >= offset && top < offset + height){
            navLinks.forEach(links => {
                links.classList.remove('active');
                document.querySelector('header nav a[href*=' + id + ']').classList.add('active');
            });
        };

    });
    let header=document.querySelector('header');

    header.classList.toggle('sticky',window.screenY>100);
    menuIcon.classList.remove('bx-x');
    navbar.classList.remove('active');
};

ScrollReveal({
    distance:'80px',
    duration:2000,
    delay:200
});

ScrollReveal().reveal('.home-content,.heading',{origin:'top'});
ScrollReveal().reveal('.home-img,.services-container,.notification,',{origin:'bottom'});
ScrollReveal().reveal('.home-content h1,.about-img',{origin:'left'});
ScrollReveal().reveal('.home-content p,.about-content',{origin:'right'});

const typed = new Typed('.multiple-text',{
    strings:['Latest book provide', 'any where and', ' any time Access', 'provide easy service', 'save your time'],
    typeSpeed:100,
    backSpeed:100,
    backDelay:1000,
    loop:true
});
document.addEventListener('DOMContentLoaded', function() {
    const settingsToggle = document.getElementById('settings-toggle');
    const themeOptions = document.getElementById('theme-options');
    
    // Toggle theme options visibility
    settingsToggle.addEventListener('click', function(e) {
        e.preventDefault();
        themeOptions.style.display = themeOptions.style.display === 'block' ? 'none' : 'block';
    });
    
    // Close theme options when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.theme-switcher')) {
            themeOptions.style.display = 'none';
        }
    });
    
    // Theme selection
    const themeOptionsElements = document.querySelectorAll('.theme-option');
    themeOptionsElements.forEach(option => {
        option.addEventListener('click', function() {
            const theme = this.getAttribute('data-theme');
            setTheme(theme);
            // Store theme preference in localStorage
            localStorage.setItem('selectedTheme', theme);
        });
    });
    
    // Check for saved theme preference
    const savedTheme = localStorage.getItem('selectedTheme');
    if (savedTheme) {
        setTheme(savedTheme);
    }
    
    function setTheme(theme) {
        document.body.className = theme;
        
        // Update active theme indicator
        themeOptionsElements.forEach(option => {
            if (option.getAttribute('data-theme') === theme) {
                option.classList.add('active-theme');
            } else {
                option.classList.remove('active-theme');
            }
        });
    }
});
