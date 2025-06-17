document.addEventListener('DOMContentLoaded', function() {
    const galleryItems = document.querySelectorAll('.gallery-item');
    const overlay = document.getElementById('gallery-overlay');
    const overlayContent = document.querySelector('.gallery-overlay-content');
    const carouselItems = document.querySelectorAll('.carousel-item');
    const closeBtn = document.querySelector('.gallery-close');
    const prevBtn = document.querySelector('.gallery-prev');
    const nextBtn = document.querySelector('.gallery-next');
    let currentIndex = 0;

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
        item.addEventListener('click', () => {
            currentIndex = index;
            updateCarousel();
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            // Check height after a short delay to ensure image is loaded
            setTimeout(checkContentHeight, 100);
        });
    });

    // Close overlay
    closeBtn.addEventListener('click', () => {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    });

    // Close overlay when clicking outside the content
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            overlay.classList.remove('active');
            document.body.style.overflow = '';
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
    window.addEventListener('resize', checkContentHeight);

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
                overlay.classList.remove('active');
                document.body.style.overflow = '';
                break;
        }
    });

    // Update carousel display
    function updateCarousel() {
        carouselItems.forEach((item, index) => {
            item.classList.toggle('active', index === currentIndex);
        });
    }
}); 