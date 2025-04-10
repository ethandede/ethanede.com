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
      gsap.to(mobileMenu, { 
        y: 0,
        duration: 0.5, 
        ease: 'power2.out',
        onStart: () => mobileMenu.classList.add('active')
      });
      gsap.to(bars[0], { y: 8, rotate: 45, duration: 0.3, ease: 'power2.out' });
      gsap.to(bars[1], { opacity: 0, duration: 0.3, ease: 'power2.out' });
      gsap.to(bars[2], { y: -8, rotate: -45, duration: 0.3, ease: 'power2.out' });
      gsap.fromTo(mobileLinks, 
        { y: 20, opacity: 0 }, 
        { y: 0, opacity: 1, duration: 0.5, stagger: 0.1, ease: 'power2.out', delay: 0.2 }
      );
    } else {
      gsap.to(mobileMenu, { 
        y: '100%',
        duration: 0.5, 
        ease: 'power2.in',
        onComplete: () => mobileMenu.classList.remove('active')
      });
      gsap.to(bars[0], { y: 0, rotate: 0, duration: 0.3, ease: 'power2.in' });
      gsap.to(bars[1], { opacity: 1, duration: 0.3, ease: 'power2.in' });
      gsap.to(bars[2], { y: 0, rotate: 0, duration: 0.3, ease: 'power2.in' });
      gsap.to(mobileLinks, { opacity: 0, duration: 0.3, ease: 'power2.in' });
    }
  });

  mobileLinks.forEach(link => {
    link.addEventListener('click', () => {
      isOpen = false;
      gsap.to(mobileMenu, { 
        y: '100%',
        duration: 0.5, 
        ease: 'power2.in',
        onComplete: () => mobileMenu.classList.remove('active')
      });
      gsap.to(bars[0], { y: 0, rotate: 0, duration: 0.3, ease: 'power2.in' });
      gsap.to(bars[1], { opacity: 1, duration: 0.3, ease: 'power2.in' });
      gsap.to(bars[2], { y: 0, rotate: 0, duration: 0.3, ease: 'power2.in' });
    });
  });
});