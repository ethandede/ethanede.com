document.addEventListener('DOMContentLoaded', () => {
  const hamburger = document.querySelector('.site-nav .hamburger');
  const mobileMenu = document.querySelector('.mobile-menu');
  const bars = document.querySelectorAll('.site-nav .bar');
  const mobileLinks = document.querySelectorAll('.mobile-nav-links a');

  if (!hamburger || !mobileMenu) {
    console.error('Hamburger or mobile menu not found');
    return;
  }

  let isOpen = false;

  hamburger.addEventListener('click', () => {
    isOpen = !isOpen;

    if (isOpen) {
      // First animate the background
      gsap.to(mobileMenu, { 
        y: 0,
        duration: 0.3, 
        ease: 'power2.out',
        onStart: () => mobileMenu.classList.add('active')
      });

      // Then animate the hamburger
      gsap.to(bars[0], { y: 8, rotate: 45, duration: 0.3, ease: 'power2.out', delay: 0.2 });
      gsap.to(bars[1], { opacity: 0, duration: 0.3, ease: 'power2.out', delay: 0.2 });
      gsap.to(bars[2], { y: -8, rotate: -45, duration: 0.3, ease: 'power2.out', delay: 0.2 });

      // Finally animate the links
      gsap.to('.mobile-nav-links', {
        opacity: 1,
        y: 0,
        duration: 0.3,
        delay: 0,
        ease: 'power2.out'
      });

      gsap.to('.mobile-nav-links li', {
        opacity: 1,
        y: 0,
        duration: 0.3,
        stagger: 0.1,
        delay: 0,
        ease: 'power2.out'
      });
    } else {
      // First fade out the links
      gsap.to('.mobile-nav-links li', {
        opacity: 0,
        y: 20,
        duration: 0.3,
        stagger: 0.05,
        ease: 'power2.in'
      });

      gsap.to('.mobile-nav-links', {
        opacity: 0,
        y: 20,
        duration: 0.3,
        ease: 'power2.in',
        onComplete: () => {
          // Then animate the hamburger
          gsap.to(bars[0], { y: 0, rotate: 0, duration: 0.3, ease: 'power2.in' });
          gsap.to(bars[1], { opacity: 1, duration: 0.3, ease: 'power2.in' });
          gsap.to(bars[2], { y: 0, rotate: 0, duration: 0.3, ease: 'power2.in' });

          // Finally slide down the menu
          gsap.to(mobileMenu, { 
            y: '100%',
            duration: 0.5, 
            ease: 'power2.in',
            onComplete: () => mobileMenu.classList.remove('active')
          });
        }
      });
    }
  });

  mobileLinks.forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const targetId = link.getAttribute('href');
      const targetElement = document.querySelector(targetId);
      
      if (targetElement) {
        isOpen = false;
        // First fade out the links
        gsap.to('.mobile-nav-links li', {
          opacity: 0,
          y: 20,
          duration: 0.3,
          stagger: 0.05,
          ease: 'power2.in'
        });

        gsap.to('.mobile-nav-links', {
          opacity: 0,
          y: 20,
          duration: 0.3,
          ease: 'power2.in',
          onComplete: () => {
            // Then animate the hamburger
            gsap.to(bars[0], { y: 0, rotate: 0, duration: 0.3, ease: 'power2.in' });
            gsap.to(bars[1], { opacity: 1, duration: 0.3, ease: 'power2.in' });
            gsap.to(bars[2], { y: 0, rotate: 0, duration: 0.3, ease: 'power2.in' });

            // Finally slide down the menu and scroll
            gsap.to(mobileMenu, { 
              y: '100%',
              duration: 0.5, 
              ease: 'power2.in',
              onComplete: () => {
                mobileMenu.classList.remove('active');
                window.scrollTo({
                  top: targetElement.offsetTop,
                  behavior: 'smooth'
                });
              }
            });
          }
        });
      }
    });
  });
});