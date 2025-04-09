document.addEventListener("DOMContentLoaded", function() {
  const isMobile = window.innerWidth < 768;
  const initialOffset = isMobile ? "100%" : "-100%";
  const persistentCta = document.querySelector(".persistent-cta");
  const contactSection = document.querySelector(".contact-section");

  ScrollTrigger.create({
    trigger: ".hero-button.cta-button",
    start: "bottom top",
    markers: false,
    onEnter: () => {
      if (!contactSection || !contactSection.classList.contains("active")) {
        gsap.fromTo(
          persistentCta,
          { y: initialOffset, opacity: 0 },
          { y: "0%", opacity: 1, duration: 0.5, ease: "power1.out" }
        );
      }
    },
    onLeaveBack: () => {
      gsap.fromTo(
        persistentCta,
        { y: "0%", opacity: 1 },
        { y: initialOffset, opacity: 0, duration: 0.5, ease: "power1.out" }
      );
    }
  });

  if (contactSection) {
    const observer = new MutationObserver(() => {
      if (contactSection.classList.contains("active")) {
        gsap.to(persistentCta, { 
          y: initialOffset, 
          opacity: 0, 
          duration: 0.3, 
          ease: "power1.out" 
        });
      } else if (window.scrollY > document.querySelector(".hero-button.cta-button").getBoundingClientRect().bottom + window.scrollY) {
        gsap.to(persistentCta, { 
          y: "0%", 
          opacity: 1, 
          duration: 0.5, 
          ease: "power1.out" 
        });
      }
    });
    observer.observe(contactSection, { attributes: true, attributeFilter: ["class"] });
  }

  // Smooth scroll for all # links, including .nav-title a[href="#home"]
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener("click", function(e) {
      e.preventDefault();
      const targetId = this.getAttribute("href");
      const target = document.querySelector(targetId);
      const offset = 80;

      if (targetId === "#contact") {
        return; // Let contact.js handle it
      }

      if (target) {
        const elementPosition = target.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.scrollY - offset;
        window.scrollTo({
          top: offsetPosition,
          behavior: "smooth"
        });
      }
    });
  });
});