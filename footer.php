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
<?php wp_footer(); ?>
</body>
</html>
