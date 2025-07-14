<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * @package EthanEde
 */

global $wp_query;

get_header();
?>
<main class="not-found-outer" style="min-height: 70vh; display: flex; align-items: center; justify-content: center;">
  <div class="container not-found__container" style="background: rgba(20, 22, 30, 0.85); border-radius: 1.25rem; box-shadow: 0 4px 32px rgba(0,0,0,0.18); padding: 2.5rem 2rem; margin: 2rem auto; max-width: 420px; text-align: center; color: var(--text-primary, #f3f3f3);">
    <div class="not-found__visual" style="margin-bottom: 1.5rem;">
      <!-- Fun SVG or animation -->
      <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <circle cx="60" cy="60" r="56" stroke="var(--primary-color, #45748C)" stroke-width="8" fill="none"/>
        <ellipse cx="60" cy="80" rx="28" ry="8" fill="var(--primary-color, #45748C)" opacity="0.15"/>
        <circle cx="45" cy="55" r="8" fill="var(--text-primary, #f3f3f3)"/>
        <circle cx="75" cy="55" r="8" fill="var(--text-primary, #f3f3f3)"/>
        <path d="M50 75 Q60 85 70 75" stroke="var(--text-primary, #f3f3f3)" stroke-width="3" fill="none" stroke-linecap="round"/>
      </svg>
    </div>
    <h1 class="not-found__title" style="font-size: 2.2rem; margin-bottom: 0.5rem; color: var(--primary-color, #45748C);">Oops! Page Not Found <span aria-hidden="true">🤔</span></h1>
    <p class="not-found__message" style="font-size: 1.1rem; margin-bottom: 1.5rem; color: var(--text-primary, #f3f3f3);">Looks like you took a wrong turn. But don’t worry, there’s plenty of awesome work to explore!</p>
    <nav class="not-found__nav" style="display: flex; flex-direction: column; gap: 0.75rem;">
      <a class="cta-button" href="<?php echo esc_url( home_url( '/work' ) ); ?>">See Latest Projects <i class="fa fa-arrow-right"></i></a>
      <a class="cta-button" style="background: rgba(255,255,255,0.08); color: var(--text-primary, #f3f3f3);" href="<?php echo esc_url( home_url( '/' ) ); ?>">Back to Home <i class="fa fa-arrow-right"></i></a>
      <a class="cta-button" style="background: rgba(255,255,255,0.08); color: var(--text-primary, #f3f3f3);" href="<?php echo esc_url( home_url( '/about' ) ); ?>">About Ethan <i class="fa fa-arrow-right"></i></a>
    </nav>
  </div>
</main>
<?php
get_footer(); 