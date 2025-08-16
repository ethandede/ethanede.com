/**
 * Performance-optimized GIF Lazy Loader
 * 
 * Delays GIF loading until cards are in viewport and user shows intent to interact
 * Optimizes for Core Web Vitals by:
 * 1. Using intersection observer for viewport detection
 * 2. Delaying GIF loading until user hovers or cards are likely to be interacted with
 * 3. Preloading only when network conditions are good
 * 4. Using requestIdleCallback for non-blocking operations
 */

class GifLazyLoader {
  constructor() {
    this.gifCache = new Map();
    this.loadedGifs = new Set();
    this.preloadQueue = [];
    this.isNetworkGood = this.checkNetworkConditions();
    this.observer = null;
    this.isMobile = this.detectMobile();
    this.mobileTriggerObserver = null;
    
    console.log('📱 Device Detection:', {
      isMobile: this.isMobile,
      isNetworkGood: this.isNetworkGood,
      userAgent: navigator.userAgent,
      touchSupport: 'ontouchstart' in window,
      maxTouchPoints: navigator.maxTouchPoints,
      windowWidth: window.innerWidth
    });
    
    this.init();
  }

  init() {
    // Wait for page load to avoid impacting LCP
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', () => {
        this.setupAfterLoad();
      });
    } else {
      this.setupAfterLoad();
    }
  }

  setupAfterLoad() {
    // Delay initialization further to avoid impacting Core Web Vitals
    requestIdleCallback(() => {
      this.setupIntersectionObserver();
      this.attachEventListeners();
    }, { timeout: 2000 });
  }

  detectMobile() {
    // Detect mobile devices for scroll-triggered GIF loading
    const isMobileUA = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    const hasTouch = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);
    const isSmallScreen = window.innerWidth <= 768;
    const isMobile = isMobileUA || hasTouch || isSmallScreen;
    
    console.log('📱 Mobile Detection Details:', {
      userAgent: isMobileUA,
      touchSupport: hasTouch,
      smallScreen: isSmallScreen,
      finalResult: isMobile
    });
    
    return isMobile;
  }

  checkNetworkConditions() {
    // Check if connection is fast enough for GIF preloading
    if ('connection' in navigator) {
      const connection = navigator.connection;
      // Only preload on fast connections, be more conservative on mobile
      const effectiveTypeGood = this.isMobile ? 
        connection.effectiveType === '4g' : 
        ['4g', '3g'].includes(connection.effectiveType);
      return effectiveTypeGood && !connection.saveData;
    }
    // Default to true if we can't detect connection, but be conservative on mobile
    return !this.isMobile;
  }

  setupIntersectionObserver() {
    const options = {
      root: null,
      rootMargin: '50px', // Start observing 50px before entering viewport
      threshold: 0.1
    };

    this.observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          this.prepareCard(entry.target);
        }
      });
    }, options);

    // Observe all cards with GIF overlays
    const cardsWithGifs = document.querySelectorAll('.card__gif-overlay[data-gif-src]');
    cardsWithGifs.forEach(card => {
      this.observer.observe(card.closest('.card'));
    });

    // Setup mobile scroll trigger observer
    if (this.isMobile) {
      this.setupMobileScrollTrigger(cardsWithGifs);
    }
  }

  setupMobileScrollTrigger(cardsWithGifs) {
    // Mobile-specific observer with different behavior
    const mobileOptions = {
      root: null,
      rootMargin: '0px', // Trigger when fully in viewport
      threshold: 0.3 // Lower threshold for easier triggering
    };

    this.mobileTriggerObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const card = entry.target;
          const gifOverlay = card.querySelector('.card__gif-overlay[data-gif-src]');
          
          console.log('📱 Mobile intersection detected:', {
            card: card,
            gifOverlay: gifOverlay,
            hasDataSrc: gifOverlay?.dataset?.gifSrc,
            isLoaded: gifOverlay?.classList?.contains('loaded'),
            isLoading: gifOverlay?.classList?.contains('loading')
          });
          
          if (gifOverlay && !gifOverlay.classList.contains('loaded') && !gifOverlay.classList.contains('loading')) {
            // Auto-load GIF when scrolled into view on mobile
            console.log('📱 Mobile scroll trigger: Loading GIF', gifOverlay.dataset.gifSrc);
            this.loadGif(gifOverlay, true);
            
            // Also show the overlay immediately on mobile
            setTimeout(() => {
              if (gifOverlay.classList.contains('loaded')) {
                console.log('📱 Mobile: Showing GIF overlay');
                gifOverlay.style.opacity = '1';
              }
            }, 800);
          }
        }
      });
    }, mobileOptions);

    // Observe all cards for mobile scroll triggers
    console.log('📱 Setting up mobile scroll triggers for', cardsWithGifs.length, 'cards');
    cardsWithGifs.forEach((gifOverlay, index) => {
      const card = gifOverlay.closest('.card');
      if (card) {
        console.log(`📱 Observing card ${index + 1}:`, card);
        this.mobileTriggerObserver.observe(card);
      }
    });
  }

  prepareCard(card) {
    const gifOverlay = card.querySelector('.card__gif-overlay[data-gif-src]');
    if (!gifOverlay || gifOverlay.dataset.prepared) return;

    // Mark as prepared to avoid duplicate processing
    gifOverlay.dataset.prepared = 'true';

    if (this.isMobile) {
      // Mobile: Add multiple trigger methods
      
      // Touch start - immediate loading
      card.addEventListener('touchstart', () => {
        console.log('📱 Touch detected on card');
        if (!gifOverlay.classList.contains('loaded') && !gifOverlay.classList.contains('loading')) {
          console.log('📱 Loading GIF on touch');
          this.loadGif(gifOverlay, true);
          
          // Show immediately on mobile
          setTimeout(() => {
            if (gifOverlay.classList.contains('loaded')) {
              gifOverlay.style.opacity = '1';
            }
          }, 300);
        }
      }, { passive: true });

      // Click handler for navigation delay
      card.addEventListener('click', (e) => {
        console.log('📱 Click detected on card');
        if (!gifOverlay.classList.contains('loaded') && !gifOverlay.classList.contains('loading')) {
          e.preventDefault();
          console.log('📱 Loading GIF on click');
          this.loadGif(gifOverlay, true);
          
          // Show overlay and then navigate
          setTimeout(() => {
            if (gifOverlay.classList.contains('loaded')) {
              gifOverlay.style.opacity = '1';
              // Navigate after showing GIF
              setTimeout(() => {
                console.log('📱 Navigating to:', card.href);
                if (card.href) {
                  window.location.href = card.href;
                }
              }, 1000);
            } else {
              // If GIF didn't load, navigate immediately
              if (card.href) {
                window.location.href = card.href;
              }
            }
          }, 500);
        }
      }, { passive: false });
    } else {
      // Desktop: Add hover listener for immediate loading
      card.addEventListener('mouseenter', () => {
        this.loadGif(gifOverlay, true);
      }, { once: false, passive: true });
    }

    // Preload on good connections after a delay (more conservative on mobile)
    if (this.isNetworkGood) {
      const delay = this.isMobile ? 5000 : 3000; // Longer delay on mobile
      setTimeout(() => {
        this.preloadGif(gifOverlay);
      }, delay);
    }
  }

  preloadGif(gifOverlay) {
    const gifSrc = gifOverlay.dataset.gifSrc;
    if (!gifSrc || this.gifCache.has(gifSrc)) return;

    // Use requestIdleCallback for non-blocking preload
    requestIdleCallback(() => {
      const img = new Image();
      img.onload = () => {
        this.gifCache.set(gifSrc, img);
        console.log(`✅ GIF preloaded: ${gifSrc.split('/').pop()}`);
      };
      img.onerror = () => {
        console.warn(`❌ Failed to preload GIF: ${gifSrc.split('/').pop()}`);
      };
      img.src = gifSrc;
    }, { timeout: 5000 });
  }

  loadGif(gifOverlay, immediate = false) {
    const gifSrc = gifOverlay.dataset.gifSrc;
    if (!gifSrc || this.loadedGifs.has(gifSrc)) return;

    // Add loading state
    gifOverlay.classList.add('loading');
    
    const loadFunction = () => {
      // Check if already cached
      if (this.gifCache.has(gifSrc)) {
        this.displayCachedGif(gifOverlay, gifSrc);
        return;
      }

      // Create and load new image
      const img = document.createElement('img');
      img.className = 'card__gif';
      img.alt = gifOverlay.closest('.card').querySelector('.card__image')?.alt + ' animated' || 'Animated GIF';
      
      img.onload = () => {
        this.onGifLoaded(gifOverlay, img, gifSrc);
      };
      
      img.onerror = () => {
        this.onGifError(gifOverlay, gifSrc);
      };

      img.src = gifSrc;
    };

    if (immediate) {
      loadFunction();
    } else {
      // Use requestIdleCallback for non-critical loads
      requestIdleCallback(loadFunction, { timeout: 1000 });
    }
  }

  displayCachedGif(gifOverlay, gifSrc) {
    const cachedImg = this.gifCache.get(gifSrc);
    const img = cachedImg.cloneNode();
    img.className = 'card__gif loaded';
    
    // Replace placeholder with loaded image
    gifOverlay.appendChild(img);
    gifOverlay.classList.remove('loading');
    gifOverlay.classList.add('loaded');
    
    this.loadedGifs.add(gifSrc);
  }

  onGifLoaded(gifOverlay, img, gifSrc) {
    // Cache the loaded image
    this.gifCache.set(gifSrc, img);
    
    // Add to overlay with fade-in effect
    img.classList.add('loaded');
    gifOverlay.appendChild(img);
    
    // Update states
    gifOverlay.classList.remove('loading');
    gifOverlay.classList.add('loaded');
    this.loadedGifs.add(gifSrc);
    
    console.log(`✅ GIF loaded and displayed: ${gifSrc.split('/').pop()}`);
  }

  onGifError(gifOverlay, gifSrc) {
    gifOverlay.classList.remove('loading');
    console.warn(`❌ Failed to load GIF: ${gifSrc.split('/').pop()}`);
    
    // Optional: Show error state or fallback
    const placeholder = gifOverlay.querySelector('.card__gif-placeholder');
    if (placeholder) {
      placeholder.style.background = 'rgba(255, 0, 0, 0.1)';
    }
  }

  attachEventListeners() {
    // Listen for network changes
    if ('connection' in navigator) {
      navigator.connection.addEventListener('change', () => {
        this.isNetworkGood = this.checkNetworkConditions();
      });
    }

    // Clean up observers on page unload
    window.addEventListener('beforeunload', () => {
      if (this.observer) {
        this.observer.disconnect();
      }
      if (this.mobileTriggerObserver) {
        this.mobileTriggerObserver.disconnect();
      }
    });
  }
}

// Initialize only if we have GIFs to load and browser supports needed features
if (window.IntersectionObserver && window.requestIdleCallback) {
  document.addEventListener('DOMContentLoaded', () => {
    const hasGifs = document.querySelector('.card__gif-overlay[data-gif-src]');
    console.log('🔍 GIF Lazy Loader: Checking for GIFs...', hasGifs);
    if (hasGifs) {
      console.log('✅ GIF Lazy Loader: Initializing...');
      new GifLazyLoader();
    } else {
      console.log('❌ GIF Lazy Loader: No GIFs found');
    }
  });
} else {
  // Fallback for older browsers - simple touch/hover loading
  console.warn('GIF lazy loading not supported, using fallback');
  document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.card__gif-overlay[data-gif-src]');
    console.log('📱 Fallback: Found', cards.length, 'cards with GIFs');
    
    cards.forEach((gifOverlay, index) => {
      const card = gifOverlay.closest('.card');
      console.log(`📱 Fallback: Setting up card ${index + 1}`);
      
      // Mobile-first approach: touch and click
      const loadGifFallback = () => {
        if (!gifOverlay.dataset.loaded) {
          console.log('📱 Fallback: Loading GIF');
          const img = document.createElement('img');
          img.src = gifOverlay.dataset.gifSrc;
          img.className = 'card__gif loaded';
          img.alt = 'Animated GIF';
          img.onload = () => {
            console.log('📱 Fallback: GIF loaded');
            gifOverlay.style.opacity = '1';
          };
          gifOverlay.appendChild(img);
          gifOverlay.classList.add('loaded');
          gifOverlay.dataset.loaded = 'true';
        }
      };
      
      // Add multiple event listeners for better mobile coverage
      card.addEventListener('touchstart', loadGifFallback, { passive: true });
      card.addEventListener('click', loadGifFallback, { passive: true });
      card.addEventListener('mouseenter', loadGifFallback, { passive: true });
      
      // Also try intersection observer if available
      if (window.IntersectionObserver) {
        const observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              console.log('📱 Fallback: Intersection detected');
              loadGifFallback();
              observer.unobserve(card);
            }
          });
        }, { threshold: 0.3 });
        observer.observe(card);
      }
    });
  });
}