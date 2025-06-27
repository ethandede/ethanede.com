<?php
/**
 * The template for displaying single blog posts.
 *
 * @package ethanede
 */
get_header();
?>

  <!-- Persistent CTA -->
  <div class="persistent-cta">
    <div class="container">
      <a href="#contact" class="cta-button contact-trigger">Let's work together <i class="fa fa-arrow-right"></i></a>
    </div>
  </div>

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
        <section class="related-projects">
          <h2>Related Projects</h2>
          <div class="deliverables-grid">
            <?php
            // Get the current post's title
            $current_title = get_the_title();

            // Query projects with the current post's title as a category
            $projects_query = new WP_Query([
              'post_type' => 'project',
              'posts_per_page' => -1,
              'tax_query' => [
                [
                  'taxonomy' => 'project_category',
                  'field' => 'name',
                  'terms' => $current_title,
                ],
              ],
            ]);

            if ($projects_query->have_posts()) :
              while ($projects_query->have_posts()) : $projects_query->the_post();
                // Get custom fields for the project
                $featured_media = get_field('featured_media');
                $role_description = get_field('role_description');
                $project_title = get_field('project_title');
                
                // Prepare description (truncate if needed)
                $description = wp_strip_all_tags($role_description);
                $description = strlen($description) > 150 ? substr($description, 0, 150) . '...' : $description;
                
                // Use master card system for related projects
                ee_render_project_card(get_the_ID(), 'single', [
                  'image_url' => $featured_media,
                  'image_alt' => $project_title,
                  'title' => $project_title,
                  'description' => $description,
                  'tags' => [] // No tags for single page project cards
                ]);
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
        <h3>How I work</h3>
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
            foreach ($categories as $category) :
              // Use master card system for sidebar category cards
              get_template_part('partials/card', null, [
                'type' => 'project',
                'context' => 'sidebar',
                'post_id' => 0, // Taxonomy-only card
                'link_url' => get_term_link($category),
                'title' => $category->name,
                'description' => $category->description ?: 'Explore ' . $category->name . ' projects',
                'show_media_types' => false
              ]);
            endforeach;
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