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
          
          // Lock body scroll to prevent background scrolling
          document.body.style.overflow = 'hidden';
          
          // Convert submit button to proper button element for consistent styling
          convertSubmitButton();
          
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
  function convertSubmitButton() {
    // Convert the CF7 input submit to a proper button element for consistent styling
    const submitBtn = document.querySelector('.wpcf7-submit');
    if (submitBtn && submitBtn.type === 'submit' && submitBtn.tagName === 'INPUT') {
      // Create new button element
      const newButton = document.createElement('button');
      newButton.type = 'submit';
      newButton.className = submitBtn.className;
      newButton.innerHTML = 'Send Message <i class="fa fa-arrow-right" aria-hidden="true"></i>';
      
      // Copy form association
      if (submitBtn.form) {
        newButton.form = submitBtn.form;
      }
      
      // Replace the input with the button
      submitBtn.parentNode.replaceChild(newButton, submitBtn);
    }
  }

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
        actionsContainer.style.marginTop = '0.75rem'; // Reduced to match CSS
        actionsContainer.style.gap = '1rem';
        actionsContainer.style.width = '100%';
        actionsContainer.style.flexShrink = '0';
        actionsContainer.style.paddingBottom = '1rem'; // Reduced to match CSS
        
        // Get the form container
        const formContainer = document.querySelector('.contact-form-container');
        
        if (formContainer) {
          // Minimize CF7 response output spacing on mobile
          const responseOutput = formContainer.querySelector('.wpcf7-response-output');
          if (responseOutput && window.innerWidth <= 768) {
            responseOutput.style.marginTop = '0.5rem';
            responseOutput.style.marginBottom = '0';
            responseOutput.style.minHeight = '0';
          }
          
          // Create a proper button element instead of cloning the input
          const submitButton = document.createElement('button');
          submitButton.type = 'submit';
          submitButton.className = 'wpcf7-submit cta-button custom-submit';
          submitButton.innerHTML = 'Send Message <i class="fa fa-arrow-right" aria-hidden="true"></i>';
          
          // Style the submit button to match cta-button
          submitButton.style.backgroundColor = 'var(--accent-color)';
          submitButton.style.color = '#fff';
          submitButton.style.border = '1px solid rgba(255, 255, 255, 0.5)';
          submitButton.style.borderRadius = '7px';
          submitButton.style.padding = '10px 20px';
          submitButton.style.fontSize = '1rem';
          submitButton.style.fontFamily = 'Merriweather, serif';
          submitButton.style.fontWeight = 'normal';
          submitButton.style.cursor = 'pointer';
          submitButton.style.transition = 'background-color 0.3s ease';
          submitButton.style.width = 'fit-content';
          submitButton.style.margin = '0';
          submitButton.style.textDecoration = 'none';
          submitButton.style.display = 'inline-block';
          
          // Clone and style the cancel button to match brand
          const clonedCancelBtn = mobileCloseBtn.cloneNode(true);
          clonedCancelBtn.innerHTML = 'Cancel <i class="fas fa-xmark" aria-hidden="true"></i>';
          clonedCancelBtn.style.backgroundColor = 'transparent';
          clonedCancelBtn.style.color = '#fff';
          clonedCancelBtn.style.border = '1px solid rgba(255, 255, 255, 0.5)';
          clonedCancelBtn.style.borderRadius = '7px';
          clonedCancelBtn.style.padding = '10px 20px';
          clonedCancelBtn.style.fontSize = '1rem';
          clonedCancelBtn.style.fontFamily = 'Merriweather, serif';
          clonedCancelBtn.style.fontWeight = 'normal';
          clonedCancelBtn.style.cursor = 'pointer';
          clonedCancelBtn.style.transition = 'all 0.3s ease';
          clonedCancelBtn.style.width = 'fit-content';
          clonedCancelBtn.style.margin = '0';
          clonedCancelBtn.style.textDecoration = 'none';
          clonedCancelBtn.style.display = 'inline-block';
          
          // COMPLETELY HIDE the original submit button and its parent paragraph
          submitBtn.style.display = 'none !important';
          submitBtn.style.visibility = 'hidden';
          submitBtn.style.opacity = '0';
          submitBtn.style.position = 'absolute';
          submitBtn.style.left = '-9999px';
          submitBtn.style.zIndex = '-999';
          submitBtn.style.pointerEvents = 'none';
          
          // Also hide the paragraph wrapper if it only contains the submit button
          const submitParent = submitBtn.closest('p');
          if (submitParent) {
            // Check if this paragraph only contains the submit button and spinner
            const otherContent = Array.from(submitParent.childNodes).filter(node => {
              return node !== submitBtn && 
                     !node.classList?.contains('wpcf7-spinner') && 
                     (node.nodeType !== Node.TEXT_NODE || 
                      (node.nodeType === Node.TEXT_NODE && node.textContent.trim() !== ''));
            });
            
            if (otherContent.length === 0) {
              submitParent.style.display = 'none';
              submitParent.style.visibility = 'hidden';
              submitParent.style.height = '0';
              submitParent.style.overflow = 'hidden';
            }
          }
          
          // Hide original mobile close button
          mobileCloseBtn.style.display = 'none';
          
          // Add both buttons to the actions container
          actionsContainer.appendChild(submitButton);
          actionsContainer.appendChild(clonedCancelBtn);
          
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
      // Unlock body scroll
      document.body.style.overflow = '';
      
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
  
  // Fix click-to-close: prevent form container clicks from bubbling, allow overlay clicks
  const contactFormContainer = document.querySelector('.contact-form-container');
  if (contactFormContainer) {
    contactFormContainer.addEventListener('click', (e) => {
      e.stopPropagation(); // Prevent clicks inside form from bubbling to overlay
    });
  }
  
  contactSection.addEventListener('click', (e) => {
      // Close when clicking anywhere in the contact section (overlay background)
      // The form container will stop propagation, so this only triggers for overlay clicks
      closeOverlay();
  });
  
  document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && contactSection.classList.contains('active')) closeOverlay();
  });

  // Contact Form 7 Event Handlers
  document.addEventListener('wpcf7mailsent', function(event) {
      // Success - email sent
      const responseOutput = event.target.querySelector('.wpcf7-response-output');
      if (responseOutput) {
          responseOutput.innerHTML = '<i class="fas fa-check-circle"></i> Thank you! Your message has been sent successfully. I\'ll get back to you soon.';
          responseOutput.style.color = '#4CAF50';
          responseOutput.style.fontWeight = 'bold';
          responseOutput.style.background = 'rgba(76, 175, 80, 0.1)';
          responseOutput.style.border = '1px solid rgba(76, 175, 80, 0.3)';
          responseOutput.style.borderRadius = '6px';
          responseOutput.style.padding = '1rem';
          responseOutput.style.marginTop = '1rem';
      }

      // Reset buttons to show success state (not spinning)
      const submitButton = event.target.querySelector('.wpcf7-submit');
      if (submitButton) {
          submitButton.innerHTML = '<i class="fas fa-check"></i> Sent!';
          submitButton.style.backgroundColor = '#4CAF50';
          submitButton.disabled = true; // Keep disabled to prevent resubmission
      }
      
      const clonedSubmitButton = document.querySelector('.form-actions-mobile .wpcf7-submit.custom-submit');
      if (clonedSubmitButton) {
          clonedSubmitButton.innerHTML = '<i class="fas fa-check"></i> Sent!';
          clonedSubmitButton.style.backgroundColor = '#4CAF50';
          clonedSubmitButton.disabled = true; // Keep disabled to prevent resubmission
      }

      // DON'T clear form immediately on success - keep it visible for user feedback
      
      // Auto-close after 4 seconds (increased for better UX)
      setTimeout(() => {
          closeOverlay();
          
          // Reset form after closing (so user doesn't see the reset)
          setTimeout(() => {
              const form = event.target;
              if (form) {
                  form.reset();
                  // Clear any validation error classes
                  const formControls = form.querySelectorAll('.wpcf7-form-control');
                  formControls.forEach(control => {
                      control.classList.remove('wpcf7-not-valid');
                  });
                  
                  // Clear response output
                  const responseOutput = form.querySelector('.wpcf7-response-output');
                  if (responseOutput) {
                      responseOutput.innerHTML = '';
                      responseOutput.style.cssText = '';
                  }
                  
                  // Reset buttons back to original state
                  const submitButton = form.querySelector('.wpcf7-submit');
                  if (submitButton) {
                      const originalContent = submitButton.getAttribute('data-original-content') || 'Send Message <i class="fa fa-arrow-right" aria-hidden="true"></i>';
                      const originalWidth = submitButton.getAttribute('data-original-width') || 'fit-content';
                      
                      submitButton.innerHTML = originalContent;
                      submitButton.style.width = originalWidth;
                      submitButton.style.minWidth = '';
                      submitButton.style.display = 'inline-block';
                      submitButton.style.backgroundColor = 'var(--accent-color)';
                      submitButton.disabled = false;
                      
                      // Clean up data attributes
                      submitButton.removeAttribute('data-original-content');
                      submitButton.removeAttribute('data-original-width');
                  }
                  
                  const clonedSubmitButton = document.querySelector('.form-actions-mobile .wpcf7-submit.custom-submit');
                  if (clonedSubmitButton) {
                      const originalContent = clonedSubmitButton.getAttribute('data-original-content') || 'Send Message <i class="fa fa-arrow-right" aria-hidden="true"></i>';
                      const originalWidth = clonedSubmitButton.getAttribute('data-original-width') || 'fit-content';
                      
                      clonedSubmitButton.innerHTML = originalContent;
                      clonedSubmitButton.style.width = originalWidth;
                      clonedSubmitButton.style.minWidth = '';
                      clonedSubmitButton.style.display = 'inline-block';
                      clonedSubmitButton.style.backgroundColor = 'var(--accent-color)';
                      clonedSubmitButton.disabled = false;
                      
                      // Clean up data attributes
                      clonedSubmitButton.removeAttribute('data-original-content');
                      clonedSubmitButton.removeAttribute('data-original-width');
                  }
              }
          }, 500); // Reset after overlay is closed
      }, 4000);
  }, false);

  document.addEventListener('wpcf7mailfailed', function(event) {
      // Email sending failed
      const responseOutput = event.target.querySelector('.wpcf7-response-output');
      if (responseOutput) {
          responseOutput.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Sorry, there was an error sending your message. Please try again or contact me directly.';
          responseOutput.style.color = '#f44336';
          responseOutput.style.fontWeight = 'bold';
          responseOutput.style.background = 'rgba(244, 67, 54, 0.1)';
          responseOutput.style.border = '1px solid rgba(244, 67, 54, 0.3)';
          responseOutput.style.borderRadius = '6px';
          responseOutput.style.padding = '1rem';
          responseOutput.style.marginTop = '1rem';
      }
  }, false);

  document.addEventListener('wpcf7spam', function(event) {
      // Spam detected
      const responseOutput = event.target.querySelector('.wpcf7-response-output');
      if (responseOutput) {
          responseOutput.innerHTML = '<i class="fas fa-shield-alt"></i> Your message was flagged by our spam filter. Please try again or contact me directly.';
          responseOutput.style.color = '#ff9800';
          responseOutput.style.fontWeight = 'bold';
          responseOutput.style.background = 'rgba(255, 152, 0, 0.1)';
          responseOutput.style.border = '1px solid rgba(255, 152, 0, 0.3)';
          responseOutput.style.borderRadius = '6px';
          responseOutput.style.padding = '1rem';
          responseOutput.style.marginTop = '1rem';
      }
  }, false);

  document.addEventListener('wpcf7invalid', function(event) {
      // Form validation failed
      const responseOutput = event.target.querySelector('.wpcf7-response-output');
      if (responseOutput) {
          responseOutput.innerHTML = '<i class="fas fa-exclamation-circle"></i> Please check the highlighted fields and try again.';
          responseOutput.style.color = '#f44336';
          responseOutput.style.fontWeight = 'bold';
          responseOutput.style.background = 'rgba(244, 67, 54, 0.1)';
          responseOutput.style.border = '1px solid rgba(244, 67, 54, 0.3)';
          responseOutput.style.borderRadius = '6px';
          responseOutput.style.padding = '1rem';
          responseOutput.style.marginTop = '1rem';
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
          // Store original content and styling
          submitButton.setAttribute('data-original-content', submitButton.innerHTML);
          submitButton.setAttribute('data-original-width', submitButton.style.width || 'fit-content');
          
          // Set elegant loading state
          submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
          submitButton.style.display = 'flex';
          submitButton.style.alignItems = 'center';
          submitButton.style.justifyContent = 'center';
          submitButton.style.gap = '0.5rem';
          submitButton.style.width = submitButton.getAttribute('data-original-width');
          submitButton.style.minWidth = '140px'; // Prevent button shrinking
          submitButton.disabled = true;
      }
      
      // Also update cloned mobile button if it exists
      const clonedSubmitButton = document.querySelector('.form-actions-mobile .wpcf7-submit.custom-submit');
      if (clonedSubmitButton) {
          // Store original content and styling
          clonedSubmitButton.setAttribute('data-original-content', clonedSubmitButton.innerHTML);
          clonedSubmitButton.setAttribute('data-original-width', clonedSubmitButton.style.width || 'fit-content');
          
          // Set elegant loading state
          clonedSubmitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
          clonedSubmitButton.style.display = 'flex';
          clonedSubmitButton.style.alignItems = 'center';
          clonedSubmitButton.style.justifyContent = 'center';
          clonedSubmitButton.style.gap = '0.5rem';
          clonedSubmitButton.style.width = clonedSubmitButton.getAttribute('data-original-width');
          clonedSubmitButton.style.minWidth = '140px'; // Prevent button shrinking
          clonedSubmitButton.disabled = true;
      }
  }, false);

  // Reset submit button after any response - ONLY for failed/invalid states
  ['wpcf7mailfailed', 'wpcf7spam', 'wpcf7invalid'].forEach(eventType => {
      document.addEventListener(eventType, function(event) {
          setTimeout(() => {
              const submitButton = event.target.querySelector('.wpcf7-submit');
              if (submitButton) {
                  // Restore original content and styling
                  const originalContent = submitButton.getAttribute('data-original-content') || 'Send Message <i class="fa fa-arrow-right" aria-hidden="true"></i>';
                  const originalWidth = submitButton.getAttribute('data-original-width') || 'fit-content';
                  
                  submitButton.innerHTML = originalContent;
                  submitButton.style.width = originalWidth;
                  submitButton.style.minWidth = '';
                  submitButton.style.display = 'inline-block'; // Reset to original display
                  submitButton.disabled = false;
                  
                  // Clean up data attributes
                  submitButton.removeAttribute('data-original-content');
                  submitButton.removeAttribute('data-original-width');
              }
              
              // Also reset cloned mobile button if it exists
              const clonedSubmitButton = document.querySelector('.form-actions-mobile .wpcf7-submit.custom-submit');
              if (clonedSubmitButton) {
                  // Restore original content and styling
                  const originalContent = clonedSubmitButton.getAttribute('data-original-content') || 'Send Message <i class="fa fa-arrow-right" aria-hidden="true"></i>';
                  const originalWidth = clonedSubmitButton.getAttribute('data-original-width') || 'fit-content';
                  
                  clonedSubmitButton.innerHTML = originalContent;
                  clonedSubmitButton.style.width = originalWidth;
                  clonedSubmitButton.style.minWidth = '';
                  clonedSubmitButton.style.display = 'inline-block'; // Reset to original display
                  clonedSubmitButton.disabled = false;
                  
                  // Clean up data attributes
                  clonedSubmitButton.removeAttribute('data-original-content');
                  clonedSubmitButton.removeAttribute('data-original-width');
              }
          }, 1000);
      }, false);
  });

  // Handle mobile close buttons (both original and cloned)
  document.addEventListener('click', (e) => {
      if (e.target.closest('.contact-close-mobile, .contact-close')) {
          e.preventDefault();
          closeOverlay();
      }
  });

  // Handle custom submit button clicks - THIS IS THE KEY FIX
  document.addEventListener('click', (e) => {
      if (e.target.closest('.form-actions-mobile .wpcf7-submit.custom-submit')) {
          e.preventDefault();
          e.stopPropagation();
          
          // Find the original CF7 submit button (the hidden input)
          const originalSubmit = document.querySelector('.contact-section .wpcf7-form input[type="submit"].wpcf7-submit');
          if (originalSubmit) {
              // Make sure the original button is properly configured
              originalSubmit.click();
          } else {
              // Fallback: try to submit the form directly
              const form = document.querySelector('.contact-section .wpcf7-form form');
              if (form) {
                  // Trigger CF7 form submission
                  form.submit();
              }
          }
      }
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