<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 */
?>
<footer class="site-footer">
  <div class="container">
    <div class="footer-content">
      <p>&copy; <?php echo date('Y'); ?> EE Creative, LLC | All rights reserved.</p>
      <nav class="footer-nav">
        <?php
        wp_nav_menu([
          'theme_location' => 'main_navigation',
          'menu_class' => '',
          'container' => false,
          'walker' => new WP_Bootstrap_Navwalker(),
        ]);
        ?>
      </nav>
    </div>
  </div>
</footer>
<!-- Contact Section (Overlay) - Global -->
<section class="contact-section" id="contact">
  <div class="contact-overlay">
    <div class="container">
      <div class="contact-form-container">
        <header class="contact-header">
          <h3>Get in Touch</h3>
          <p>Please fill out the form below and I'll get back to you as soon as possible.</p>
          <button class="contact-close">×</button>
        </header>
        <?php echo do_shortcode('[contact-form-7 id="eb95201" title="Contact Form - Ethan Ede"]'); ?>
        <button class="contact-close-mobile contact-close" style="display: none;">
          <i class="fas fa-times-circle"></i> Cancel
        </button>
      </div>
    </div>
  </div>
</section>

<?php wp_footer(); ?>
</body>
</html>
