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
          <h2>Related work</h2>
          <div class="deliverables-grid">
            <?php
            // Get the current post's title
            $current_title = get_the_title();

            // Get the current post's project_category terms
            $current_categories = get_the_terms(get_the_ID(), 'project_category');
            $show_only_projects = false;
            $video_animation_query = false;
            $query_terms = $current_title;
            
            if ($current_categories && !is_wp_error($current_categories)) {
              foreach ($current_categories as $cat) {
                if (
                  strtolower($cat->name) === 'website management' ||
                  $cat->slug === 'website-management'
                ) {
                  $show_only_projects = true;
                  break;
                }
              }
            }

            // Special handling for Video & Animation posts
            if (
              strtolower($current_title) === 'video & animation' ||
              strtolower($current_title) === 'video and animation' ||
              strpos(strtolower($current_title), 'video') !== false && strpos(strtolower($current_title), 'animation') !== false
            ) {
              $video_animation_query = true;
              // Query for both video and animation related terms
              $query_terms = ['Animations', 'Video Production', 'Animation', 'Video'];
            }

            // Query projects with appropriate terms
            if ($video_animation_query) {
              $projects_query = new WP_Query([
                'post_type' => 'project',
                'posts_per_page' => -1,
                'tax_query' => [
                  [
                    'taxonomy' => 'project_category',
                    'field' => 'name',
                    'terms' => $query_terms,
                    'operator' => 'IN',
                  ],
                ],
              ]);
            } else {
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
            }

            $related_items = [];
            $deliverable_ids = [];

            if ($projects_query->have_posts()) :
              while ($projects_query->have_posts()) : $projects_query->the_post();
                $project_id = get_the_ID();
                // Add project to related items
                $related_items[] = [
                  'type' => 'project',
                  'id' => $project_id
                ];

                // Only add deliverables if not Website Management
                if (!$show_only_projects) {
                  $project_deliverables = get_field('project_deliverables', $project_id);
                  if ($project_deliverables && is_array($project_deliverables)) {
                    foreach ($project_deliverables as $deliverable) {
                      if (is_object($deliverable)) {
                        $deliverable_id = $deliverable->ID;
                      } else {
                        $deliverable_id = $deliverable;
                      }
                      // Avoid duplicates
                      if (!in_array($deliverable_id, $deliverable_ids)) {
                        $related_items[] = [
                          'type' => 'deliverable',
                          'id' => $deliverable_id
                        ];
                        $deliverable_ids[] = $deliverable_id;
                      }
                    }
                  }
                }
              endwhile;
              wp_reset_postdata();
            endif;

            // For Video & Animation posts, also query deliverables directly
            if ($video_animation_query && !$show_only_projects) {
              $deliverables_query = new WP_Query([
                'post_type' => 'deliverable',
                'posts_per_page' => -1,
                'tax_query' => [
                  [
                    'taxonomy' => 'deliverable_type',
                    'field' => 'name',
                    'terms' => ['Animations', 'Video Production', 'Animation', 'Video'],
                    'operator' => 'IN',
                  ],
                ],
              ]);

              if ($deliverables_query->have_posts()) :
                while ($deliverables_query->have_posts()) : $deliverables_query->the_post();
                  $deliverable_id = get_the_ID();
                  // Avoid duplicates
                  if (!in_array($deliverable_id, $deliverable_ids)) {
                    $related_items[] = [
                      'type' => 'deliverable',
                      'id' => $deliverable_id
                    ];
                    $deliverable_ids[] = $deliverable_id;
                  }
                endwhile;
                wp_reset_postdata();
              endif;
            }

            if (!empty($related_items)) :
              // Optionally shuffle for more mixing
              // shuffle($related_items);
              foreach ($related_items as $item) {
                if ($item['type'] === 'project') {
                  // Get custom fields for the project
                  $featured_media = get_field('featured_media', $item['id']);
                  $role_description = get_field('role_description', $item['id']);
                  $project_title = get_field('project_title', $item['id']);
                  // Prepare description with fallbacks
                  $description = '';
                  if ($role_description) {
                      $description = wp_strip_all_tags($role_description);
                  } elseif (get_field('project_excerpt', $item['id'])) {
                      $description = get_field('project_excerpt', $item['id']);
                  } elseif (get_the_excerpt($item['id'])) {
                      $description = get_the_excerpt($item['id']);
                  } else {
                      $project_description = get_field('project_description', $item['id']);
                      if ($project_description) {
                          $description = wp_strip_all_tags($project_description);
                      }
                  }
                  $description = mb_strlen($description, 'UTF-8') > 180 ? mb_substr($description, 0, 180, 'UTF-8') . '...' : $description;
                  
                  // Get properly sized image for card
                  $card_image_url = '';
                  if ($featured_media) {
                      // Try to get the card-thumbnail size from the featured media
                      $attachment_id = attachment_url_to_postid($featured_media);
                      if ($attachment_id) {
                          $card_image_url = wp_get_attachment_image_url($attachment_id, 'card-thumbnail');
                      }
                      // Fallback to original if no thumbnail available
                      if (!$card_image_url) {
                          $card_image_url = $featured_media;
                      }
                  }
                  
                  ee_render_project_card($item['id'], 'single', [
                    'image_url' => $card_image_url,
                    'image_alt' => $project_title,
                    'title' => $project_title,
                    'description' => $description,
                    'tags' => [] // No tags for single page project cards
                  ]);
                } elseif ($item['type'] === 'deliverable') {
                  // Get deliverable image: use deliverable_featured_image first, then post thumbnail
                  $featured_media = get_field('deliverable_featured_image', $item['id']);
                  if (!$featured_media && has_post_thumbnail($item['id'])) {
                    $featured_media = get_the_post_thumbnail_url($item['id'], 'card-thumbnail');
                  }
                  
                  // Get properly sized image for card
                  $card_image_url = '';
                  if ($featured_media) {
                      // Try to get the card-thumbnail size from the featured media
                      $attachment_id = attachment_url_to_postid($featured_media);
                      if ($attachment_id) {
                          $card_image_url = wp_get_attachment_image_url($attachment_id, 'card-thumbnail');
                      }
                      // Fallback to original if no thumbnail available
                      if (!$card_image_url) {
                          $card_image_url = $featured_media;
                      }
                  }
                  
                  $deliverable_title = get_the_title($item['id']);
                  $excerpt = get_field('deliverable_excerpt', $item['id']);
                  $description = $excerpt ?: get_the_excerpt($item['id']);
                  // Get deliverable type for tags
                  $tags = [];
                  $type_terms = get_the_terms($item['id'], 'deliverable_type');
                  if ($type_terms && !is_wp_error($type_terms)) {
                    $type_term = $type_terms[0];
                    if (function_exists('get_singular_term_display_name')) {
                      $tags[] = get_singular_term_display_name($type_term->name);
                    } else {
                      $tags[] = $type_term->name;
                    }
                  }
                  ee_render_deliverable_card($item['id'], 'single', [
                    'image_url' => $card_image_url,
                    'image_alt' => $deliverable_title,
                    'title' => $deliverable_title,
                    'description' => $description,
                    'tags' => $tags
                  ]);
                }
              }
            else : ?>
              <p class="supporting-text">No associated projects<?php echo $show_only_projects ? '' : ' or deliverables'; ?> found.</p>
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