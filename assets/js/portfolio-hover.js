document.addEventListener("DOMContentLoaded", function() {
  // Check if GSAP is loaded
  if (typeof gsap === 'undefined') {
    console.log('GSAP not loaded');
    return;
  }
  
  console.log('Portfolio hover script loaded');
  
  // Handle main portfolio/project/deliverable cards (35px movement)
  // Support both old classes and new master card system
  // Prioritize wrapper elements (.deliverable-card) over inner elements (.card)
  const wrapperItems = document.querySelectorAll(`
    .portfolio-item:not(.single-sidebar .portfolio-item), 
    .project-card, 
    .deliverable-card
  `);
  
  // Add standalone cards that aren't wrapped and aren't sidebar cards
  const standaloneCards = document.querySelectorAll('.card:not(.single-sidebar .card):not(.card--sidebar):not(.blog-sidebar .card):not(.article-sidebar .card)');
  const standaloneCardsFiltered = Array.from(standaloneCards).filter(card => {
    // Only include if it's not inside a wrapper we already selected
    // Also exclude any cards that have the sidebar class or are in sidebar containers
    return !card.closest('.deliverable-card, .project-card, .portfolio-item') && 
           !card.classList.contains('card--sidebar') &&
           !card.closest('.blog-sidebar, .article-sidebar, .single-sidebar');
  });
  
  // Combine both arrays
  const portfolioItems = [...Array.from(wrapperItems), ...standaloneCardsFiltered];
  
  console.log('Main portfolio items found:', portfolioItems.length);
  portfolioItems.forEach(function(item, index) {
    console.log(`Main portfolio item ${index}:`, item.className);
    // Support both old and new arrow selectors
    const arrow = item.querySelector(`
      .portfolio-arrow i, 
      .project-card-arrow i, 
      .deliverable-card__arrow i,
      .card__arrow i,
      .card__arrow svg
    `);

    if (arrow) {
      // Ensure arrow is visible and positioned correctly initially
      gsap.set(arrow, {
        x: 0,
        opacity: 1,
        visibility: 'visible'
      });
    }
    
    item.addEventListener('mouseenter', function() {
      // Find overlay and image elements for additional effects
      const overlay = item.querySelector('.card__overlay, .deliverable-card__overlay');
      const image = item.querySelector('.card__image'); // Be more specific
      const thumbnail = item.querySelector('.deliverable-card__thumbnail');
      const cardArrow = item.querySelector('.deliverable-card__arrow');
      const gifOverlay = item.querySelector('.card__gif-overlay'); // Find GIF overlay
      
      // Determine box-shadow values based on current styling
      let hoverShadow = "0 6px 12px rgba(0, 0, 0, 0.3)";
      let defaultShadow = "0 4px 8px rgba(0, 0, 0, 0.2)";
      
      // Check if this is the newer card style (has border)
      const computedStyle = window.getComputedStyle(item);
      if (computedStyle.borderWidth !== '0px') {
        hoverShadow = "0 12px 40px rgba(0, 0, 0, 0.15)";
        defaultShadow = "0 4px 6px rgba(0, 0, 0, 0.1)";
      }
      
      // Animate card with clean lift and shadow (matching original CSS exactly)
      gsap.to(item, {
        duration: 0.3,
        y: -5,
        boxShadow: hoverShadow,
        ease: "power2.out"
      });
      
      // Animate overlay opacity if it exists
      if (overlay) {
        gsap.to(overlay, {
          duration: 0.3,
          opacity: 0.15,
          ease: "power2.out"
        });
      }
      
      // Animate GIF overlay if it exists (for home portfolio cards)
      if (gifOverlay) {
        // Only animate if the GIF is loaded or loading
        if (gifOverlay.classList.contains('loaded') || gifOverlay.classList.contains('loading')) {
          gsap.to(gifOverlay, {
            duration: 0.3,
            opacity: 1,
            ease: "power2.out"
          });
        }
      }
      
      // Animate image filter if it exists (work-archive style) - but not if there's a GIF
      if (image && !thumbnail && !gifOverlay) {
        gsap.to(image, {
          duration: 0.3,
          filter: "grayscale(100%)",
          ease: "power2.out"
        });
      }
      
      // Animate image/thumbnail scale if it exists (project-cards style)
      if (image && thumbnail) {
        gsap.to(image, {
          duration: 0.3,
          scale: 1.05,
          ease: "power2.out"
        });
      }
      
      if (thumbnail) {
        gsap.to(thumbnail, {
          duration: 0.3,
          scale: 1.05,
          ease: "power2.out"
        });
      }
      
      // Animate card-specific arrow (project-cards style)
      if (cardArrow) {
        gsap.to(cardArrow, {
          duration: 0.3,
          opacity: 1,
          scale: 1,
          ease: "power2.out"
        });
        
        const arrowSvg = cardArrow.querySelector('svg');
        if (arrowSvg) {
          gsap.to(arrowSvg, {
            duration: 0.3,
            rotation: 45,
            ease: "power2.out"
          });
        }
      }
      
      // Animate main arrow if it exists (standard style)
      if (arrow) {
        gsap.to(arrow, {
          duration: 0.5,
          x: 35,
          ease: "power1.out"
        });
      }
    });
    
    item.addEventListener('mouseleave', function() {
      // Find overlay and image elements for reset
      const overlay = item.querySelector('.card__overlay, .deliverable-card__overlay');
      const image = item.querySelector('.card__image'); // Be more specific
      const thumbnail = item.querySelector('.deliverable-card__thumbnail');
      const cardArrow = item.querySelector('.deliverable-card__arrow');
      const gifOverlay = item.querySelector('.card__gif-overlay'); // Find GIF overlay
      
      // Determine default box-shadow values
      let defaultShadow = "0 4px 8px rgba(0, 0, 0, 0.2)";
      
      // Check if this is the newer card style (has border)
      const computedStyle = window.getComputedStyle(item);
      if (computedStyle.borderWidth !== '0px') {
        defaultShadow = "0 4px 6px rgba(0, 0, 0, 0.1)";
      }
      
      // Return card to original state (matching original CSS exactly)
      gsap.to(item, {
        duration: 0.3,
        y: 0,
        boxShadow: defaultShadow,
        ease: "power2.out"
      });
      
      // Reset overlay opacity if it exists
      if (overlay) {
        gsap.to(overlay, {
          duration: 0.3,
          opacity: 0,
          ease: "power2.out"
        });
      }
      
      // Reset GIF overlay if it exists (for home portfolio cards)
      if (gifOverlay) {
        // Only animate if the GIF is loaded or loading
        if (gifOverlay.classList.contains('loaded') || gifOverlay.classList.contains('loading')) {
          gsap.to(gifOverlay, {
            duration: 0.3,
            opacity: 0,
            ease: "power2.out"
          });
        }
      }
      
      // Reset image filter if it exists (work-archive style) - but not if there's a GIF
      if (image && !thumbnail && !gifOverlay) {
        gsap.to(image, {
          duration: 0.3,
          filter: "grayscale(0%)",
          ease: "power2.out"
        });
      }
      
      // Reset image/thumbnail scale if it exists (project-cards style)
      if (image && thumbnail) {
        gsap.to(image, {
          duration: 0.3,
          scale: 1,
          ease: "power2.out"
        });
      }
      
      if (thumbnail) {
        gsap.to(thumbnail, {
          duration: 0.3,
          scale: 1,
          ease: "power2.out"
        });
      }
      
      // Reset card-specific arrow (project-cards style)
      if (cardArrow) {
        gsap.to(cardArrow, {
          duration: 0.3,
          opacity: 0,
          scale: 0.8,
          ease: "power2.out"
        });
        
        const arrowSvg = cardArrow.querySelector('svg');
        if (arrowSvg) {
          gsap.to(arrowSvg, {
            duration: 0.3,
            rotation: 0,
            ease: "power2.out"
          });
        }
      }
      
      // Return main arrow to original position if it exists (standard style)
      if (arrow) {
        gsap.to(arrow, {
          duration: 0.3,
          x: 0,
          ease: "bounce.out"
        });
      }
    });
  });

  // Handle sidebar post cards (8px movement)
  // Support both old classes and new master card system
  const sidebarItems = document.querySelectorAll(`
    .single-sidebar .portfolio-item,
    .single-sidebar .card,
    .blog-sidebar .card--sidebar,
    .article-sidebar .card--sidebar,
    .card--sidebar
  `);
  
  console.log('Sidebar items found:', sidebarItems.length);
  sidebarItems.forEach(function(item, index) {
    console.log(`Sidebar item ${index}:`, item.className);
    // Support both old and new arrow selectors
    const arrow = item.querySelector(`
      .portfolio-arrow i,
      .card__arrow i,
      .card__arrow svg
    `);

    if (arrow) {
      item.addEventListener('mouseenter', function() {
        // Animate card hover effects for sidebar cards - subtle for small size
        gsap.to(item, {
          duration: 0.25,
          y: -2, // Very subtle lift for small cards
          boxShadow: "0 4px 12px rgba(0, 0, 0, 0.08)",
          ease: "power2.out"
        });
        
        // Animate arrow movement
        gsap.to(arrow, {
          duration: 0.5,
          x: 8,
          ease: "power1.out"
        });
        
        // No image zoom for sidebar cards - keep clean and simple
      });
      
      item.addEventListener('mouseleave', function() {
        // Reset card hover effects - faster for small cards
        gsap.to(item, {
          duration: 0.25,
          y: 0,
          boxShadow: "0 2px 6px rgba(0, 0, 0, 0.06)", // Lighter default shadow
          ease: "power2.out"
        });
        
        // Reset arrow position
        gsap.to(arrow, {
          duration: 0.3,
          x: 0,
          ease: "bounce.out"
        });
        
        // No image reset needed for sidebar cards
      });
    }
  });
});
