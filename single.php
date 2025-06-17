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
  <?php get_template_part('partials/site-navigation'); ?>

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
                $featured_media = get_field('featured_media');
                $role_description = get_field('role_description');
                ?>
                <a href="<?php the_permalink(); ?>" class="project-card-link">
                  <div class="project-card">
                    <div class="project-card-overlay"></div>
                    <?php if ($featured_media) : ?>
                      <img src="<?php echo esc_url($featured_media); ?>" alt="<?php echo esc_attr(get_field('project_title')); ?>">
                    <?php endif; ?>
                    <div class="project-card-copy">
                      <h3><?php echo esc_html(get_field('project_title')); ?></h3>
                      <p><?php 
                        $description = wp_strip_all_tags($role_description);
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
          // Get all project categories
          $categories = get_terms([
            'taxonomy' => 'project_category',
            'hide_empty' => true,
            'orderby' => 'name',
            'order' => 'ASC'
          ]);

          if (!empty($categories) && !is_wp_error($categories)) :
            foreach ($categories as $category) : ?>
              <a href="<?php echo get_term_link($category); ?>" class="sidebar-post-card">
                <div class="portfolio-item">
                  <h4><?php echo esc_html($category->name); ?></h4>
                  <div class="portfolio-arrow">
                    <i class="fas fa-arrow-right"></i>
                  </div>
                </div>
              </a>
            <?php endforeach;
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