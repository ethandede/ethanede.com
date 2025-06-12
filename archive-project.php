<?php
/**
 * Template for displaying project archives
 *
 * @package ethanede
 */

get_header();
?>

<main id="main" class="min-h-screen bg-background">
  <!-- Navigation -->
  <nav class="fixed top-0 left-0 w-full z-50 bg-background/95 backdrop-blur-sm shadow-sm">
    <div class="container mx-auto px-4">
      <div class="flex items-center justify-between h-16">
        <h3 class="text-xl font-bold">
          <a href="<?php echo esc_url(home_url('/')); ?>" class="text-text-primary hover:text-accent transition-colors">
            Ethan Ede
          </a>
        </h3>
        <button class="hamburger p-2" aria-label="Toggle mobile menu">
          <span class="bar top block w-6 h-0.5 bg-text-primary mb-1.5 transition-transform"></span>
          <span class="bar middle block w-6 h-0.5 bg-text-primary mb-1.5 transition-opacity"></span>
          <span class="bar bottom block w-6 h-0.5 bg-text-primary transition-transform"></span>
        </button>
        <?php
        wp_nav_menu([
          'theme_location' => 'main_navigation',
          'menu_class' => 'hidden md:flex space-x-6',
          'container' => false,
          'walker' => new WP_Bootstrap_Navwalker(),
        ]);
        ?>
      </div>
    </div>
  </nav>

  <!-- Mobile Menu Overlay -->
  <div class="mobile-menu fixed inset-0 bg-background/95 backdrop-blur-sm z-50 hidden">
    <?php
    wp_nav_menu([
      'theme_location' => 'main_navigation',
      'menu_class' => 'flex flex-col items-center justify-center h-full space-y-6 text-xl',
      'container' => false,
      'walker' => new WP_Bootstrap_Navwalker(),
    ]);
    ?>
  </div>

  <!-- Color controls UI -->
  <?php get_template_part('partials/color-controls'); ?>

  <!-- Hero Section -->
  <section class="pt-32 pb-16 md:pt-40 md:pb-24">
    <div class="container mx-auto px-4">
      <div class="max-w-4xl mx-auto text-center">
        <h1>Projects</h1>
        <p class="supporting-text mt-4">Explore my portfolio of web development, design, and digital strategy projects.</p>
      </div>
    </div>
  </section>

  <!-- Projects Grid Section -->
  <section class="py-12 md:py-16">
    <div class="container mx-auto px-4">
      <div class="project-cards">
        <?php
        if (have_posts()) :
          while (have_posts()) : the_post();
            $image = get_field('project_image');
            $description = get_field('project_description');
            ?>
            <a href="<?php the_permalink(); ?>" class="project-card-link">
              <div class="project-card">
                <div class="project-card-overlay"></div>
                <?php if ($image) : ?>
                  <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                <?php endif; ?>
                <div class="project-card-copy">
                  <h3><?php the_title(); ?></h3>
                  <p><?php 
                    $description = wp_strip_all_tags($description);
                    echo strlen($description) > 150 ? substr($description, 0, 150) . '...' : $description;
                  ?></p>
                </div>
                <div class="project-card-arrow">
                  <i class="fas fa-arrow-right"></i>
                </div>
              </div>
            </a>
          <?php
          endwhile;

          // Pagination
          echo '<div class="pagination mt-8">';
          echo paginate_links([
            'prev_text' => '<i class="fas fa-chevron-left"></i>',
            'next_text' => '<i class="fas fa-chevron-right"></i>',
          ]);
          echo '</div>';

        else :
          ?>
          <p class="text-text-secondary text-center"><?php _e('No projects found.', 'ethanede'); ?></p>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Contact Section (Overlay) -->
  <?php get_template_part('partials/contact'); ?>
</main>

<?php get_footer(); ?> 