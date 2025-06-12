<?php
/**
 * The template for displaying single blog posts.
 *
 * @package ethanede
 */
get_header();
?>

<!-- Background Animation Container -->
<div class="background-animation">
  <svg class="animated-squares" viewBox="0 0 1920 1080" preserveAspectRatio="xMidYMid slice"></svg>
</div>

<main id="main" class="site-main">

  <!-- Navigation -->
  <nav class="site-nav">
    <div class="container">
      <div class="nav-content">
        <h3 class="nav-title"><a href="<?php echo esc_url(home_url('/')); ?>">Ethan Ede</a></h3>
        <button class="hamburger" aria-label="Toggle mobile menu">
          <span class="bar top"></span>
          <span class="bar middle"></span>
          <span class="bar bottom"></span>
        </button>
        <?php
        wp_nav_menu([
          'theme_location' => 'main_navigation',
          'menu_class' => 'nav-links',
          'container' => false,
          'walker' => new WP_Bootstrap_Navwalker(),
        ]);
        ?>
      </div>
    </div>
  </nav>

  <!-- Persistent CTA -->
  <div class="persistent-cta">
    <div class="container">
      <a href="#contact" class="cta-button contact-trigger">Let's work together <i class="fa fa-arrow-right"></i></a>
    </div>
  </div>

  <!-- Mobile Menu Overlay -->
  <div class="mobile-menu">
    <?php
    wp_nav_menu([
      'theme_location' => 'main_navigation',
      'menu_class' => 'mobile-nav-links',
      'container' => false,
      'walker' => new WP_Bootstrap_Navwalker(),
    ]);
    ?>
  </div>

  <!-- Color controls UI -->
  <?php get_template_part('partials/color-controls'); ?>

  <!-- Hero Section -->
  <section class="hero">
    <div class="container">
      <div class="hero-content">
        <?php
        if (have_posts()) :
          while (have_posts()) : the_post();
            ?>
            <h1><?php the_title(); ?></h1>
            <?php
          endwhile;
          rewind_posts();
        endif;
        ?>
      </div>
    </div>
  </section>

  <!-- Post Content Section with Sidebar -->
  <section class="single-layout">
    <div class="container">
      <div class="single-main">
        <?php
        if (have_posts()) :
          while (have_posts()) : the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
              <div class="entry-content">
                <?php the_content(); ?>
              </div>
            </article>
            <?php
          endwhile;
        else :
          ?>
          <p class="supporting-text"><?php _e('No content found.', 'ethanede'); ?></p>
          <?php
        endif;
        ?>

        <!-- Associated Projects Section -->
        <section class="associated-projects">
          <h2>Related Projects</h2>
          <div class="project-cards">
            <?php
            // Get the current post's title
            $current_title = get_the_title();

            // Query projects with the current post's title as a category or tag
            $projects_query = new WP_Query([
              'post_type' => 'project',
              'posts_per_page' => -1,
              'tax_query' => [
                'relation' => 'OR',
                [
                  'taxonomy' => 'project_category',
                  'field' => 'name',
                  'terms' => $current_title,
                ],
                [
                  'taxonomy' => 'project_tag',
                  'field' => 'name',
                  'terms' => $current_title,
                ],
              ],
            ]);

            if ($projects_query->have_posts()) :
              while ($projects_query->have_posts()) : $projects_query->the_post();
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
              wp_reset_postdata();
            else :
              ?>
              <p class="supporting-text">No associated projects found.</p>
            <?php endif; ?>
          </div>
        </section>
      </div>
      <aside class="single-sidebar">
        <h3>How I Work</h3>
        <div class="sidebar-posts">
          <?php
          // Test string to confirm sidebar code execution
          echo "SIDEBAR TEST";

          // Query for other posts
          $sidebar_query = new WP_Query([
            'post_type' => 'post',
            'posts_per_page' => 5,
            'post__not_in' => [get_the_ID()],
            'ignore_sticky_posts' => 1
          ]);
          if ($sidebar_query->have_posts()) :
            while ($sidebar_query->have_posts()) : $sidebar_query->the_post(); ?>
              <a href="<?php the_permalink(); ?>" class="sidebar-post-card">
                <div class="portfolio-item">
                  <h4><?php the_title(); ?></h4>
                  <div class="portfolio-arrow">
                    <i class="fas fa-arrow-right"></i>
                  </div>
                </div>
              </a>
            <?php endwhile;
            wp_reset_postdata();
          endif;
          ?>
        </div>
      </aside>
    </div>
  </section>

  <!-- Contact Section (Overlay) -->
  <?php get_template_part('partials/contact'); ?>
</main>

<?php get_footer(); ?>