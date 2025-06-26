document.addEventListener('DOMContentLoaded', () => {
  const triggers = document.querySelectorAll('.contact-trigger, a[href="#contact"]');
  const contactSection = document.querySelector('.contact-section');
  const closeBtn = document.querySelector('.contact-close');
  const formContainer = document.querySelector('.contact-form-container');

  if (!contactSection) return;

  // Open overlay
  triggers.forEach(trigger => {
      trigger.addEventListener('click', (e) => {
          e.preventDefault();
          
          // Setup mobile layout if needed (only when contact form opens)
          if (window.innerWidth <= 768) {
            setupMobileLayout();
          }
          
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

  // Setup mobile layout for bottom close button
  function setupMobileLayout() {
    try {
      const mobileCloseBtn = document.querySelector('.contact-close-mobile');
      const submitBtn = document.querySelector('.contact-section .wpcf7-submit');
    
      if (mobileCloseBtn && submitBtn && !document.querySelector('.form-actions-mobile')) {
        // Show mobile close button
        mobileCloseBtn.style.display = 'inline-flex';
        
        // Create a flex container for submit and close buttons
        const actionsContainer = document.createElement('div');
        actionsContainer.className = 'form-actions-mobile';
        actionsContainer.style.display = 'flex';
        actionsContainer.style.justifyContent = 'center';
        actionsContainer.style.alignItems = 'center';
        actionsContainer.style.marginTop = '1.5rem';
        actionsContainer.style.gap = '1rem';
        actionsContainer.style.width = '100%';
        
        // Get the form container
        const formContainer = document.querySelector('.contact-form-container');
        
        if (formContainer) {
          // Preserve submit button styling and move it
          const clonedSubmitBtn = submitBtn.cloneNode(true);
          
          // Ensure the submit button has proper styling
          clonedSubmitBtn.style.backgroundColor = 'var(--accent-color)';
          clonedSubmitBtn.style.color = '#fff';
          clonedSubmitBtn.style.border = '1px solid rgba(255, 255, 255, 0.5)';
          clonedSubmitBtn.style.borderRadius = '6px';
          clonedSubmitBtn.style.padding = '0.75rem 1.5rem';
          clonedSubmitBtn.style.fontSize = '1rem';
          clonedSubmitBtn.style.fontFamily = 'Merriweather, serif';
          clonedSubmitBtn.style.cursor = 'pointer';
          clonedSubmitBtn.style.transition = 'all 0.3s ease';
          clonedSubmitBtn.style.width = 'fit-content';
          
          // Hide original submit button
          submitBtn.style.display = 'none';
          
          // Add both buttons to the actions container
          actionsContainer.appendChild(clonedSubmitBtn);
          actionsContainer.appendChild(mobileCloseBtn.cloneNode(true));
          
          // Hide the original mobile close button
          mobileCloseBtn.style.display = 'none';
          
          // Add actions container to form container
          formContainer.appendChild(actionsContainer);
        }
      }
    } catch (error) {
      console.warn('Error setting up mobile contact layout:', error);
    }
  }

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

  // Contact Form 7 Event Handlers
  document.addEventListener('wpcf7mailsent', function(event) {
      // Success - email sent
      const responseOutput = event.target.querySelector('.wpcf7-response-output');
      if (responseOutput) {
          responseOutput.innerHTML = '<i class="fad fa-check-circle"></i> Thank you! Your message has been sent successfully. I\'ll get back to you soon.';
          responseOutput.style.color = '#4CAF50';
          responseOutput.style.fontWeight = 'bold';
      }

      // Clear form after successful submission
      setTimeout(() => {
          const form = event.target;
          if (form) {
              form.reset();
              // Clear any validation error classes
              const formControls = form.querySelectorAll('.wpcf7-form-control');
              formControls.forEach(control => {
                  control.classList.remove('wpcf7-not-valid');
              });
          }
      }, 100);

      // Auto-close after 3 seconds
      setTimeout(() => {
          closeOverlay();
      }, 3000);
  }, false);

  document.addEventListener('wpcf7mailfailed', function(event) {
      // Email sending failed
      const responseOutput = event.target.querySelector('.wpcf7-response-output');
      if (responseOutput) {
          responseOutput.innerHTML = '<i class="fal fa-exclamation-triangle"></i> Sorry, there was an error sending your message. Please try again or contact me directly.';
          responseOutput.style.color = '#f44336';
          responseOutput.style.fontWeight = 'bold';
      }
  }, false);

  document.addEventListener('wpcf7spam', function(event) {
      // Spam detected
      const responseOutput = event.target.querySelector('.wpcf7-response-output');
      if (responseOutput) {
          responseOutput.innerHTML = '<i class="fass fa-shield-check"></i> Your message was flagged by our spam filter. Please try again or contact me directly.';
          responseOutput.style.color = '#ff9800';
          responseOutput.style.fontWeight = 'bold';
      }
  }, false);

  document.addEventListener('wpcf7invalid', function(event) {
      // Form validation failed
      const responseOutput = event.target.querySelector('.wpcf7-response-output');
      if (responseOutput) {
          responseOutput.innerHTML = '<i class="fal fa-exclamation-circle"></i> Please check the highlighted fields and try again.';
          responseOutput.style.color = '#f44336';
          responseOutput.style.fontWeight = 'bold';
      }

      // Scroll to first invalid field
      const firstInvalidField = event.target.querySelector('.wpcf7-not-valid');
      if (firstInvalidField) {
          firstInvalidField.focus();
          // Add a subtle shake animation to draw attention
          firstInvalidField.style.animation = 'shake 0.5s ease-in-out';
          setTimeout(() => {
              firstInvalidField.style.animation = '';
          }, 500);
      }
  }, false);

  document.addEventListener('wpcf7submit', function(event) {
      // Form submitted (before processing)
      const submitButton = event.target.querySelector('.wpcf7-submit');
      if (submitButton) {
          submitButton.innerHTML = '<i class="fal fa-spinner fa-spin"></i> Sending...';
          submitButton.disabled = true;
      }
  }, false);

  // Reset submit button after any response
  ['wpcf7mailsent', 'wpcf7mailfailed', 'wpcf7spam', 'wpcf7invalid'].forEach(eventType => {
      document.addEventListener(eventType, function(event) {
          setTimeout(() => {
              const submitButton = event.target.querySelector('.wpcf7-submit');
              if (submitButton) {
                  submitButton.innerHTML = 'Send Message <i class="fa fa-arrow-right"></i>';
                  submitButton.disabled = false;
              }
          }, 1000);
      }, false);
  });
});

// Add CSS for shake animation
const style = document.createElement('style');
style.textContent = `
  @keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
  }
`;
document.head.appendChild(style);