const hamburger = document.querySelector(".hamburger-menu");
const navMenu = document.querySelector(".nav-menu");

if(hamburger) {
  hamburger.addEventListener("click", () => {
    hamburger.classList.toggle("active");
    navMenu.classList.toggle("active");
  });
}


const hamburger2 = document.querySelector(".hamburger-menu");
const navMenu2 = document.querySelector(".nav-menu");

if(hamburger2) {
  hamburger2.addEventListener("click", () => {
    hamburger2.classList.toggle("active");
    navMenu2.classList.toggle("active");
  });
}

jQuery(document).ready(($) => {
  $('.product_short_description .open_long').on('click', function(e) {
    e.preventDefault();
    $(this).hide();
    $('.full_description').slideDown();
  });
})



document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const headerMenu = document.querySelector('.header-menu');
    const menuOverlay = document.querySelector('.menu-overlay');
    const hamburger = document.querySelector('.hamburger');
    const body = document.body;

    if (mobileMenuToggle && headerMenu && menuOverlay && hamburger) {
        
        // Toggle mobile menu
        function toggleMobileMenu() {
            const isOpen = headerMenu.classList.contains('menu-open');
            
            if (isOpen) {
                // Close menu
                headerMenu.classList.remove('menu-open');
                menuOverlay.classList.remove('overlay-active');
                hamburger.classList.remove('active');
                body.style.overflow = '';
            } else {
                // Open menu
                headerMenu.classList.add('menu-open');
                menuOverlay.classList.add('overlay-active');
                hamburger.classList.add('active');
                body.style.overflow = 'hidden'; // Prevent background scrolling
            }
        }

        // Click events
        mobileMenuToggle.addEventListener('click', toggleMobileMenu);
        menuOverlay.addEventListener('click', toggleMobileMenu);

        // Close menu when clicking on menu links
        const navLinks = document.querySelectorAll('.header-menu .nav-list a');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 768) { // Only on mobile
                    toggleMobileMenu();
                }
            });
        });

        // Close menu on window resize if it's open
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768 && headerMenu.classList.contains('menu-open')) {
                headerMenu.classList.remove('menu-open');
                menuOverlay.classList.remove('overlay-active');
                hamburger.classList.remove('active');
                body.style.overflow = '';
            }
        });

        // Close menu on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && headerMenu.classList.contains('menu-open')) {
                toggleMobileMenu();
            }
        });
    }
});