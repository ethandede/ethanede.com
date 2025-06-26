document.addEventListener('DOMContentLoaded', function() {
  const hamburger = document.querySelector('.hamburger');
  const mobileMenu = document.querySelector('.mobile-menu');
  const mobileLinks = document.querySelectorAll('.mobile-menu__links a');
  let isMenuOpen = false;

  function toggleMenu() {
    const bars = hamburger.querySelectorAll('.bar');
    
    if (!isMenuOpen) {
      // Open menu
      isMenuOpen = true;
      
      // Animate hamburger to X
      gsap.to(bars[0], { rotation: 45, y: 8, duration: 0.3 });
      gsap.to(bars[1], { opacity: 0, duration: 0.3 });
      gsap.to(bars[2], { rotation: -45, y: -8, duration: 0.3 });
      
      // Show mobile menu with slide up animation
      mobileMenu.classList.add('active');
      
      // Animate menu links
      gsap.fromTo('.mobile-menu__links', 
        { 
          opacity: 0,
          y: 20
        },
        { 
          opacity: 1,
          y: 0,
          duration: 0.4,
          delay: 0.1
        }
      );
      
      // Animate individual links with stagger
      gsap.fromTo('.mobile-menu__links li', 
        { 
          opacity: 0,
          y: 20
        },
        { 
          opacity: 1,
          y: 0,
          duration: 0.3,
          stagger: 0.05,
          delay: 0.2
        }
      );
      
    } else {
      // Close menu
      isMenuOpen = false;
      
      // Animate hamburger back to normal
      gsap.to(bars[0], { rotation: 0, y: 0, duration: 0.3 });
      gsap.to(bars[1], { opacity: 1, duration: 0.3 });
      gsap.to(bars[2], { rotation: 0, y: 0, duration: 0.3 });
      
      // Hide mobile menu with slide down animation
      gsap.to('.mobile-menu__links', {
        opacity: 0,
        y: 20,
        duration: 0.3,
        onComplete: () => {
          mobileMenu.classList.remove('active');
        }
      });
    }
  }

  // Close menu when clicking outside
  document.addEventListener('click', function(event) {
    if (isMenuOpen && !mobileMenu.contains(event.target) && !hamburger.contains(event.target)) {
      // Animate links out first
      gsap.to('.mobile-menu__links li', {
        opacity: 0,
        y: -20,
        duration: 0.2,
        stagger: 0.03
      });
      
      gsap.to('.mobile-menu__links', {
        opacity: 0,
        y: 20,
        duration: 0.3,
        delay: 0.1,
        onComplete: () => {
          mobileMenu.classList.remove('active');
          
          // Reset hamburger
          const bars = hamburger.querySelectorAll('.bar');
          gsap.to(bars[0], { rotation: 0, y: 0, duration: 0.3 });
          gsap.to(bars[1], { opacity: 1, duration: 0.3 });
          gsap.to(bars[2], { rotation: 0, y: 0, duration: 0.3 });
          
          isMenuOpen = false;
        }
      });
    }
  });

  // Event listeners
  if (hamburger) {
    hamburger.addEventListener('click', toggleMenu);
  }

  // Close menu when clicking on links
  mobileLinks.forEach(link => {
    link.addEventListener('click', () => {
      if (isMenuOpen) {
        toggleMenu();
      }
    });
  });

  // Handle window resize
  window.addEventListener('resize', function() {
    if (window.innerWidth > 768 && isMenuOpen) {
      // Reset everything on larger screens
      mobileMenu.classList.remove('active');
      const bars = hamburger.querySelectorAll('.bar');
      gsap.set(bars[0], { rotation: 0, y: 0 });
      gsap.set(bars[1], { opacity: 1 });
      gsap.set(bars[2], { rotation: 0, y: 0 });
      isMenuOpen = false;
    }
  });
});