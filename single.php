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

  <!-- Single Header Section -->
  <section class="single-header">
    <div class="container">
      <div class="single-header__content">
        <?php
        if (have_posts()) :
          while (have_posts()) : the_post();
            ?>
            <?php ee_display_single_page_tag(); ?>
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
          <h2>Related Work</h2>
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
                
                // Prepare description with fallbacks
                $description = '';
                if ($role_description) {
                    $description = wp_strip_all_tags($role_description);
                } elseif (get_field('project_excerpt')) {
                    $description = get_field('project_excerpt');
                } elseif (get_the_excerpt()) {
                    $description = get_the_excerpt();
                } else {
                    // Fallback to project description
                    $project_description = get_field('project_description');
                    if ($project_description) {
                        $description = wp_strip_all_tags($project_description);
                    }
                }
                
                // Truncate if needed (use mb_strlen for proper UTF-8 character counting)
                $description = mb_strlen($description, 'UTF-8') > 180 ? mb_substr($description, 0, 180, 'UTF-8') . '...' : $description;
                
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
        <h3 class="single-sidebar__title">How I work</h3>
        
        <?php
        // Debug: Let's see what categories we're getting
        $categories = get_terms([
          'taxonomy' => 'project_category',
          'hide_empty' => true,
          'orderby' => 'name',
          'order' => 'ASC'
        ]);
        
        echo "<!-- Debug: Found " . count($categories) . " categories -->";
        
        if (!empty($categories) && !is_wp_error($categories)) :
          foreach ($categories as $category) :
            $category_image = get_field('category_image', 'project_category_' . $category->term_id);
            echo "<!-- Debug: Category: " . $category->name . " (ID: " . $category->term_id . ") | Image: " . ($category_image ? $category_image : 'NO IMAGE') . " -->";
          endforeach;
        endif;
        ?>
        
        <div class="sidebar-posts">
          <?php
          // Get the current post's category to exclude it
          $current_post_categories = get_the_terms(get_the_ID(), 'project_category');
          $current_category_names = [];
          
          if ($current_post_categories && !is_wp_error($current_post_categories)) {
            $current_category_names = array_map(function($cat) {
              return $cat->name;
            }, $current_post_categories);
          }
          
          // Get all project categories ordered by ACF field_display_order field
          $categories = get_terms([
            'taxonomy' => 'project_category',
            'hide_empty' => true,
            'meta_key' => 'field_display_order',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'fields' => 'all'
          ]);
          
          // Fallback to name ordering if no field_display_order field is set
          if (empty($categories)) {
            $categories = get_terms([
              'taxonomy' => 'project_category',
              'hide_empty' => true,
              'orderby' => 'name',
              'order' => 'ASC',
              'fields' => 'all'
            ]);
          }

          if (!empty($categories) && !is_wp_error($categories)) :
            foreach ($categories as $category) :
              // Skip if this category matches the current post's category
              if (in_array($category->name, $current_category_names)) {
                continue;
              }
              
              // Get the category image from ACF field on the taxonomy term
              $category_image = get_field('category_image', 'project_category_' . $category->term_id);
              
              // Find the corresponding regular post for linking by slug
              $slug_variations = [];
              
              // Standard slug conversion
              $slug_variations[] = sanitize_title($category->name);
              
              // Replace & with "and" - handle different spacing patterns
              if (strpos($category->name, '&') !== false) {
                $clean_name = html_entity_decode($category->name, ENT_QUOTES, 'UTF-8');
                $slug_variations[] = sanitize_title(str_replace('&', 'and', $clean_name));
                $slug_variations[] = sanitize_title(str_replace(' & ', ' and ', $clean_name));
                $slug_variations[] = sanitize_title(str_replace(' & ', '-and-', $clean_name));
              }
              
              // Replace + with "and" 
              if (strpos($category->name, '+') !== false) {
                $clean_name = html_entity_decode($category->name, ENT_QUOTES, 'UTF-8');
                $slug_variations[] = sanitize_title(str_replace('+', 'and', $clean_name));
                $slug_variations[] = sanitize_title(str_replace(' + ', ' and ', $clean_name));
                $slug_variations[] = sanitize_title(str_replace(' + ', '-and-', $clean_name));
              }
              
              // Try each slug variation
              $category_post = [];
              foreach ($slug_variations as $slug) {
                if (!empty($slug)) {
                  $category_post = get_posts([
                    'post_type' => 'post',
                    'posts_per_page' => 1,
                    'name' => $slug,
                    'post_status' => 'publish'
                  ]);
                  
                  // If found, break out of loop
                  if (!empty($category_post)) {
                    break;
                  }
                }
              }

              // Get the link - either to the post or the category archive as fallback
              $link = !empty($category_post) ? get_permalink($category_post[0]->ID) : get_term_link($category);
              
              // Use master card system for sidebar category cards
              get_template_part('partials/card', null, [
                'type' => 'project',
                'context' => 'sidebar',
                'post_id' => !empty($category_post) ? $category_post[0]->ID : 0, // Use actual post ID if found
                'link_url' => $link,
                'image_url' => $category_image, // Add the category image
                'image_alt' => $category->name,
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