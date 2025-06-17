<?php
/**
 * Template for displaying project archives
 *
 * @package ethanede
 */

get_header();

// Debug information
global $wp_query;
echo '<!-- Debug: Post Type: ' . get_post_type() . ' -->';
echo '<!-- Debug: Is Archive: ' . (is_post_type_archive('project') ? 'Yes' : 'No') . ' -->';
echo '<!-- Debug: Found Posts: ' . $wp_query->found_posts . ' -->';
?>

<main id="main" class="min-h-screen bg-background">
  <!-- Color controls UI -->
  <?php get_template_part('partials/color-controls'); ?>

  <!-- Hero Section -->
  <section class="hero pt-32 pb-16 md:pt-40 md:pb-24">
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
            $image = get_field('featured_media');
            $description = get_field('role_description');
            $tags = get_the_terms(get_the_ID(), 'project_tag');
            ?>
            <a href="<?php the_permalink(); ?>" class="project-card-link">
              <div class="project-card">
                <div class="project-card-overlay"></div>
                <?php if ($image) : ?>
                  <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                <?php endif; ?>
                <div class="project-card-copy">
                  <h3><?php the_title(); ?></h3>
                  <p><?php 
                    $description = wp_strip_all_tags($description);
                    echo strlen($description) > 150 ? substr($description, 0, 150) . '...' : $description;
                  ?></p>
                  <?php if ($tags && !is_wp_error($tags)) : ?>
                    <div class="project-card-tags">
                      <?php foreach ($tags as $tag) : ?>
                        <span class="tag"><?php echo esc_html($tag->name); ?></span>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
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
            'type' => 'list',
            'class' => 'pagination-list'
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