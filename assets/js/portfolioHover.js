document.addEventListener("DOMContentLoaded", function() {
  // Handle main portfolio items (35px movement)
  const portfolioItems = document.querySelectorAll('.portfolio-item:not(.single-sidebar .portfolio-item)');
  portfolioItems.forEach(function(item) {
    const arrow = item.querySelector('.portfolio-arrow i');

    item.addEventListener('mouseenter', function() {
      gsap.to(arrow, {
        duration: 0.5,
        x: 35,
        ease: "power1.out"
      });
    });
    
    item.addEventListener('mouseleave', function() {
      gsap.to(arrow, {
        duration: 0.3,
        x: 0,
        ease: "bounce.out"
      });
    });
  });

  // Handle sidebar post cards (8px movement)
  const sidebarItems = document.querySelectorAll('.single-sidebar .portfolio-item');
  sidebarItems.forEach(function(item) {
    const arrow = item.querySelector('.portfolio-arrow i');

    item.addEventListener('mouseenter', function() {
      gsap.to(arrow, {
        duration: 0.5,
        x: 8,
        ease: "power1.out"
      });
    });
    
    item.addEventListener('mouseleave', function() {
      gsap.to(arrow, {
        duration: 0.3,
        x: 0,
        ease: "bounce.out"
      });
    });
  });
});
