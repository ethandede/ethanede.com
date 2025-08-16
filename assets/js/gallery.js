document.addEventListener('DOMContentLoaded', function() {
    const galleryItems = document.querySelectorAll('.gallery-item');
    const overlay = document.getElementById('gallery-overlay');
    const overlayContent = document.querySelector('.gallery-overlay-content');
    const carouselItems = document.querySelectorAll('.carousel-item');
    const closeBtn = document.querySelector('.gallery-close');
    const prevBtn = document.querySelector('.gallery-prev');
    const nextBtn = document.querySelector('.gallery-next');
    let currentIndex = 0;
    
    // Early return if gallery elements don't exist
    if (!overlay || !overlayContent || galleryItems.length === 0) {
        console.log('Gallery elements not found - skipping gallery initialization');
        return;
    }

    // Handle video poster frame interactions
    function initializePosterFrames() {
        const posterContainers = document.querySelectorAll('.video-poster-container');
        
        posterContainers.forEach(container => {
            const playOverlay = container.querySelector('.video-play-overlay');
            const video = container.querySelector('.gallery-video');
            const poster = container.querySelector('.video-poster');
            
            if (playOverlay && video && poster) {
                // Add debugging function to see what CSS is affecting the overlay
                window.debugOverlay = function() {
                    console.log('🔍 OVERLAY DEBUG INFO:');
                    console.log('Overlay element:', playOverlay);
                    console.log('Overlay computed styles:', window.getComputedStyle(playOverlay));
                    console.log('Overlay inline styles:', playOverlay.style.cssText);
                    console.log('Overlay background:', window.getComputedStyle(playOverlay).background);
                    console.log('Overlay display:', window.getComputedStyle(playOverlay).display);
                    console.log('Overlay visibility:', window.getComputedStyle(playOverlay).visibility);
                    console.log('Overlay opacity:', window.getComputedStyle(playOverlay).opacity);
                };
                
                playOverlay.addEventListener('click', function(e) {
                    // Check if we're on mobile (multiple checks for reliability)
                    const isMobile = window.innerWidth < 768 || 
                                   window.matchMedia('(max-width: 768px)').matches ||
                                   /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                    
                    if (isMobile) {
                        // Mobile: Simple inline video playback, no overlay
                        e.stopPropagation(); // Prevent any gallery interactions
                        e.preventDefault(); // Prevent any default actions
                        
                        // Force hide poster and play button immediately
                        poster.style.display = 'none';
                        poster.style.visibility = 'hidden';
                        
                        // NUCLEAR option: Force hide overlay completely with all possible CSS overrides
                        console.log('🎯 HIDING OVERLAY INITIALLY');
                        console.log('Overlay before hide:', playOverlay.style.cssText);
                        playOverlay.style.cssText = 'display: none !important; visibility: hidden !important; opacity: 0 !important; background: transparent !important; background-image: none !important; pointer-events: none !important;';
                        console.log('Overlay after hide:', playOverlay.style.cssText);
                        
                        // Double-check after a brief delay and during video events
                        const forceHideOverlay = () => {
                            console.log('🔥 FORCE HIDING OVERLAY');
                            console.log('Overlay computed style background:', window.getComputedStyle(playOverlay).background);
                            console.log('Overlay computed style display:', window.getComputedStyle(playOverlay).display);
                            playOverlay.style.cssText = 'display: none !important; visibility: hidden !important; opacity: 0 !important; background: transparent !important; background-image: none !important; pointer-events: none !important;';
                            console.log('Overlay after force hide:', playOverlay.style.cssText);
                            
                            // NUCLEAR OPTION: Find ANY element in the video container that has a gradient
                            const videoContainer = container;
                            const allElements = videoContainer.querySelectorAll('*');
                            allElements.forEach(el => {
                                const computedStyle = window.getComputedStyle(el);
                                const beforeStyle = window.getComputedStyle(el, '::before');
                                const afterStyle = window.getComputedStyle(el, '::after');
                                
                                // Check for gradients in background-image
                                if (computedStyle.backgroundImage && computedStyle.backgroundImage.includes('gradient')) {
                                    console.log('🎯 FOUND GRADIENT ELEMENT:', el, computedStyle.backgroundImage);
                                    el.style.cssText += 'background-image: none !important; background: transparent !important;';
                                }
                                
                                // Check ::before and ::after for gradients (can't modify directly but can log)
                                if (beforeStyle.backgroundImage && beforeStyle.backgroundImage.includes('gradient')) {
                                    console.log('🎯 FOUND GRADIENT ::BEFORE:', el, beforeStyle.backgroundImage);
                                    el.style.cssText += 'position: relative;';
                                    // Add a CSS class to hide ::before
                                    el.classList.add('hide-gradient-before');
                                }
                                
                                if (afterStyle.backgroundImage && afterStyle.backgroundImage.includes('gradient')) {
                                    console.log('🎯 FOUND GRADIENT ::AFTER:', el, afterStyle.backgroundImage);
                                    el.style.cssText += 'position: relative;';
                                    // Add a CSS class to hide ::after
                                    el.classList.add('hide-gradient-after');
                                }
                            });
                        };
                        
                        setTimeout(forceHideOverlay, 50);
                        setTimeout(forceHideOverlay, 100);
                        setTimeout(forceHideOverlay, 200);
                        
                        // Show and play video inline with iOS compatibility
                        video.style.display = 'block';
                        video.style.visibility = 'visible';
                        video.controls = true;
                        
                        // Essential iOS attributes
                        video.setAttribute('playsinline', '');
                        video.setAttribute('webkit-playsinline', '');
                        video.setAttribute('preload', 'metadata');
                        
                        // Optional control restrictions
                        video.setAttribute('controlsList', 'nodownload nofullscreen noremoteplayback');
                        video.setAttribute('disablePictureInPicture', '');
                        
                        // Load and play video
                        video.load(); // Ensure video is loaded with new attributes
                        video.play().catch(e => {
                            console.log('Video play failed:', e);
                            // Fallback: just show controls and let user manually play
                        });
                        
                        // Add event listeners to force hide overlay during video events
                        video.addEventListener('play', () => {
                            console.log('📹 VIDEO PLAY EVENT');
                            forceHideOverlay();
                        });
                        video.addEventListener('playing', () => {
                            console.log('📹 VIDEO PLAYING EVENT');
                            forceHideOverlay();
                        });
                        video.addEventListener('timeupdate', () => {
                            // Only log occasionally to avoid spam
                            if (Math.floor(video.currentTime) % 2 === 0) {
                                console.log('📹 VIDEO TIMEUPDATE:', video.currentTime);
                                forceHideOverlay();
                            }
                        });
                        video.addEventListener('loadstart', () => {
                            console.log('📹 VIDEO LOADSTART');
                            forceHideOverlay();
                        });
                        video.addEventListener('canplay', () => {
                            console.log('📹 VIDEO CANPLAY');
                            forceHideOverlay();
                        });
                        
                        // Reset only when video truly ends, not when paused
                        const resetVideo = () => {
                            poster.style.display = 'block';
                            poster.style.visibility = 'visible';
                            playOverlay.style.display = 'flex';
                            playOverlay.style.visibility = 'visible';
                            playOverlay.style.opacity = '1';
                            video.style.display = 'none';
                            video.style.visibility = 'hidden';
                            video.currentTime = 0;
                        };
                        
                        // Only reset when video ends, not when paused
                        video.addEventListener('ended', resetVideo);
                        
                        // Optional: Reset if user clicks away or video loses focus
                        // But don't reset on simple pause events
                        video.addEventListener('pause', (e) => {
                            // Only reset if video has ended (currentTime equals duration)
                            if (video.ended || video.currentTime >= video.duration) {
                                resetVideo();
                            }
                        });
                    } else {
                        // Desktop: Open in gallery overlay immediately
                        // Don't stop propagation - let the gallery item click handler run
                        // The gallery will open with the video, and we can detect it's a video in the overlay
                    }
                });
            }
        });
    }

    // Initialize poster frames
    initializePosterFrames();

    // Function to check if content is taller than viewport
    function checkContentHeight() {
        const activeItem = document.querySelector('.carousel-item.active');
        if (activeItem) {
            const contentHeight = activeItem.offsetHeight;
            const viewportHeight = window.innerHeight;
            
            if (contentHeight > viewportHeight) {
                overlayContent.classList.add('tall-content');
            } else {
                overlayContent.classList.remove('tall-content');
            }
        }
    }

    // Open overlay when clicking a gallery item
    galleryItems.forEach((item, index) => {
        item.addEventListener('click', (e) => {
            // Check if we're on mobile
            const isMobile = window.innerWidth < 768;
            
            // On mobile, completely disable gallery overlay
            if (isMobile) {
                e.preventDefault(); // Prevent any default link behavior
                return false;
            }
            
            currentIndex = index;
            updateCarousel();
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            // Check height after a short delay to ensure content is loaded
            setTimeout(() => {
                checkContentHeight();
                
                // Auto-play video if this gallery item has a video
                const activeCarouselItem = document.querySelector('.carousel-item.active');
                if (activeCarouselItem) {
                    // Check for video in the overlay carousel (simple video element)
                    const carouselVideo = activeCarouselItem.querySelector('video');
                    if (carouselVideo) {
                        // Ensure iOS compatibility attributes
                        carouselVideo.setAttribute('playsinline', '');
                        carouselVideo.setAttribute('webkit-playsinline', '');
                        carouselVideo.setAttribute('preload', 'metadata');
                        
                        // Auto-play video in carousel
                        carouselVideo.play().catch(e => {
                            console.log('Auto-play failed:', e);
                        });
                    }
                }
            }, 100);
        });
    });

    // Close overlay
    closeBtn.addEventListener('click', () => {
        closeGalleryOverlay();
    });

    // Close overlay when clicking outside the content
    overlay.addEventListener('click', (e) => {
        // Check if click was on the overlay background or overlay-content (but not on interactive elements)
        const isOverlayBackground = e.target === overlay;
        const isOverlayContent = e.target === overlayContent;
        const isCarousel = e.target.classList.contains('gallery-carousel');
        const isInteractiveElement = e.target.closest('.carousel-item, .gallery-close, .gallery-prev, .gallery-next, video, img');
        
        if ((isOverlayBackground || isOverlayContent || isCarousel) && !isInteractiveElement) {
            closeGalleryOverlay();
        }
    });

    // Previous button
    prevBtn.addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + carouselItems.length) % carouselItems.length;
        updateCarousel();
        setTimeout(checkContentHeight, 100);
    });

    // Next button
    nextBtn.addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % carouselItems.length;
        updateCarousel();
        setTimeout(checkContentHeight, 100);
    });

    // Handle window resize
    window.addEventListener('resize', () => {
        checkContentHeight();
        
        // No special mobile cleanup needed with simplified approach
    });

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (!overlay.classList.contains('active')) return;

        switch(e.key) {
            case 'ArrowLeft':
                currentIndex = (currentIndex - 1 + carouselItems.length) % carouselItems.length;
                updateCarousel();
                setTimeout(checkContentHeight, 100);
                break;
            case 'ArrowRight':
                currentIndex = (currentIndex + 1) % carouselItems.length;
                updateCarousel();
                setTimeout(checkContentHeight, 100);
                break;
            case 'Escape':
                closeGalleryOverlay();
                break;
        }
    });

    // Update carousel display
    function updateCarousel() {
        carouselItems.forEach((item, index) => {
            const wasActive = item.classList.contains('active');
            item.classList.toggle('active', index === currentIndex);
            
            // Reset videos in non-active items
            if (wasActive && index !== currentIndex) {
                // Reset carousel videos (simple video elements)
                const carouselVideo = item.querySelector('video');
                if (carouselVideo) {
                    carouselVideo.pause();
                    carouselVideo.currentTime = 0;
                }
                
                // Also reset gallery videos with poster containers (for completeness)
                const videoPosterContainer = item.querySelector('.video-poster-container');
                if (videoPosterContainer) {
                    const video = videoPosterContainer.querySelector('.gallery-video');
                    const poster = videoPosterContainer.querySelector('.video-poster');
                    const playOverlay = videoPosterContainer.querySelector('.video-play-overlay');
                    
                    if (video && poster && playOverlay) {
                        video.pause();
                        video.currentTime = 0;
                        video.style.display = 'none';
                        poster.style.display = 'block';
                        playOverlay.style.display = 'flex';
                    }
                }
            }
        });
        
        // Auto-play video in newly active item
        setTimeout(() => {
            const activeItem = document.querySelector('.carousel-item.active');
            if (activeItem) {
                // Check for video in the overlay carousel (simple video element)
                const carouselVideo = activeItem.querySelector('video');
                if (carouselVideo) {
                    // Ensure iOS compatibility attributes
                    carouselVideo.setAttribute('playsinline', '');
                    carouselVideo.setAttribute('webkit-playsinline', '');
                    carouselVideo.setAttribute('preload', 'metadata');
                    
                    // Auto-play video in carousel
                    carouselVideo.play().catch(e => {
                        console.log('Auto-play failed:', e);
                    });
                }
            }
        }, 50);
    }

    // Centralized function to close gallery overlay and stop all videos
    function closeGalleryOverlay() {
        // Stop all videos in the carousel
        const allCarouselVideos = overlay.querySelectorAll('video');
        allCarouselVideos.forEach(video => {
            if (!video.paused) {
                video.pause();
            }
        });
        
        // Also stop any gallery videos with poster containers
        const allGalleryVideos = document.querySelectorAll('.video-poster-container .gallery-video');
        allGalleryVideos.forEach(video => {
            if (!video.paused) {
                video.pause();
            }
        });
        
        // Close the overlay
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
}); 