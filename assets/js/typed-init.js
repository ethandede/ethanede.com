document.addEventListener("DOMContentLoaded", function() {
  // Only initialize Typed.js if the target element exists (homepage only)
  const rotatingWordElement = document.querySelector('.rotating-word');
  
  if (rotatingWordElement) {
    var typed = new Typed('.rotating-word', {
      strings: ["converts", "engages", "evolves", "works"],
      typeSpeed: 50,
      backSpeed: 5,
      backDelay: 5000,
      loop: false,
      smartBackspace: false,
      showCursor: true,
      cursorChar: '.'
    });
  }
});
