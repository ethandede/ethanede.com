document.addEventListener('DOMContentLoaded', () => {
  const triggers = document.querySelectorAll('.contact-trigger, .nav-links a[href="#contact"]');
  const contactSection = document.querySelector('.contact-section');
  const closeBtn = document.querySelector('.contact-close');
  const formContainer = document.querySelector('.contact-form-container');

  if (!contactSection) return;

  // Open overlay
  triggers.forEach(trigger => {
      trigger.addEventListener('click', (e) => {
          e.preventDefault();
          gsap.to(contactSection, { 
              autoAlpha: 1, // Combines opacity and visibility
              duration: 0.5,
              ease: "power2.out",
              onStart: () => contactSection.classList.add('active')
          });
          gsap.fromTo(
              formContainer,
              { y: 50, opacity: 0 },
              { y: 0, opacity: 1, duration: 0.7, ease: "power2.out", delay: 0.1 }
          );
          window.scrollTo({ top: 0, behavior: 'smooth' });
      });
  });

  // Close overlay
  const closeOverlay = () => {
      gsap.to(contactSection, { 
          autoAlpha: 0,
          duration: 0.5,
          ease: "power2.in",
          onComplete: () => contactSection.classList.remove('active')
      });
      gsap.to(formContainer, { 
          y: 50, 
          opacity: 0, 
          duration: 0.4, 
          ease: "power2.in" 
      });
  };

  if (closeBtn) closeBtn.addEventListener('click', closeOverlay);
  contactSection.addEventListener('click', (e) => {
      if (e.target === contactSection.querySelector('.contact-overlay')) closeOverlay();
  });
  document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && contactSection.classList.contains('active')) closeOverlay();
  });
});