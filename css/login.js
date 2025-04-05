
function toggleForms() {
    const signupForm = document.getElementById('signup');
    const signinForm = document.getElementById('signin');

  
    if (signupForm.style.display === 'none') {
        signupForm.style.display = 'block';
        signinForm.style.display = 'none';
    } else {
        signupForm.style.display = 'none';
        signinForm.style.display = 'block';
    }
}

// Ensure the forms are responsive
window.addEventListener('resize', function() {
    const formsContainer = document.querySelector('.container');
    if (window.innerWidth < 768) {
        formsContainer.style.flexDirection = 'column'; 
        formsContainer.style.flexDirection = 'row'; 
    }
});

// Call the resize function on page load
window.dispatchEvent(new Event('resize'));
// Scroll Down Button
document.getElementById('scroll-down').addEventListener('click', function() {
    window.scrollBy({
      top: window.innerHeight,
      behavior: 'smooth'
    });
  });
  
  // Scroll Top Button
  window.addEventListener('scroll', function() {
    const scrollTopBtn = document.getElementById('scroll-top');
    if (window.pageYOffset > 300) {
      scrollTopBtn.classList.add('active');
    } else {
      scrollTopBtn.classList.remove('active');
    }
  });
  
  document.getElementById('scroll-top').addEventListener('click', function() {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });