// Sidebar Accordion for Tags Sections

document.addEventListener('DOMContentLoaded', function () {
  const triggers = document.querySelectorAll('.tags-accordion .accordion-trigger');

  triggers.forEach(trigger => {
    trigger.addEventListener('click', function () {
      const targetId = trigger.getAttribute('data-target');
      const content = document.getElementById(targetId);
      const icon = trigger.querySelector('.accordion-icon');
      const isOpen = content.classList.contains('is-open');

      // Close all accordions in this sidebar
      document.querySelectorAll('.tags-accordion .accordion-content').forEach(el => {
        el.classList.remove('is-open');
      });
      document.querySelectorAll('.tags-accordion .accordion-trigger').forEach(el => {
        el.classList.remove('is-open');
      });

      // Open this one if it was closed
      if (!isOpen) {
        content.classList.add('is-open');
        trigger.classList.add('is-open');
      }
    });
  });
}); 