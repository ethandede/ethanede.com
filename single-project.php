<?php
/**
 * Template for displaying single project posts.
 *
 * @package ethanede
 */

get_header();
?>

<main class="main single-project">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <!-- Project Header Section -->
        <section class="project-header">
            <div class="container">
                <div class="project-header__content">
                    <?php ee_display_single_page_tag(); ?>
                    <h1 class="project-header__title"><?php echo esc_html(get_the_title()); ?></h1>
                </div>
            </div>
        </section>

        <!-- Project Content -->
        <section class="single-layout">
            <div class="container">
                <div class="single-content-wrapper">
                    <article class="single-main">
                        <div class="project-description">
                            <h2>Overview</h2>
                            <?php echo wp_kses_post(get_field('project_description')); ?>
                        </div>

                        <?php if ($featured_media = get_field('featured_media')) : ?>
                            <div class="project-featured-media">
                                <?php
                                $file_type = wp_check_filetype($featured_media);
                                if (strpos($file_type['type'], 'video') !== false) : ?>
                                    <div class="decorative-squares-wrapper">
                                        <video class="project-featured-video" controls
                                               src="<?php echo esc_url($featured_media); ?>"
                                               playsinline
                                               webkit-playsinline
                                               preload="metadata">
                                            <source src="<?php echo esc_url($featured_media); ?>" type="<?php echo esc_attr($file_type['type']); ?>">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                <?php else : ?>
                                    <div class="decorative-squares-wrapper">
                                        <div class="decorative-squares-bg">
                                            <div class="square square-1"></div>
                                            <div class="square square-2"></div>
                                            <div class="square square-3"></div>
                                            <div class="square square-4"></div>
                                            <div class="square square-5"></div>
                                            <div class="square square-6"></div>
                                            <div class="square square-7"></div>
                                            <div class="square square-8"></div>
                                            <div class="square square-9"></div>
                                            <div class="square square-10"></div>
                                            <div class="square square-11"></div>
                                            <div class="square square-12"></div>
                                        </div>
                                        <img src="<?php echo esc_url($featured_media); ?>" 
                                             alt="<?php echo esc_attr(get_field('project_title')); ?>" 
                                             class="project-featured-image">
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($key_responsibilities = get_field('key_responsibilities')) : ?>
                            <div class="project-responsibilities">
                                <h3>Key Responsibilities</h3>
                                <ul>
                                    <?php foreach ($key_responsibilities as $responsibility) : ?>
                                        <li><?php echo esc_html($responsibility['responsibility']); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if ($deliverables = get_field('project_deliverables')) : ?>
                            <div class="project-deliverables">
                                <h3>Related Deliverables</h3>
                                <div class="deliverables-grid">
                                    <?php foreach ($deliverables as $deliverable) : 
                                                                // Get deliverable media for master card system
                        $featured_media = '';
                        if (has_post_thumbnail($deliverable->ID)) {
                            $featured_media = get_the_post_thumbnail_url($deliverable->ID, 'card-thumbnail');
                        } else {
                            // Check for deliverable featured image field
                            $deliverable_featured = get_field('deliverable_featured_image', $deliverable->ID);
                            if ($deliverable_featured) {
                                // Get properly sized image for card
                                $attachment_id = attachment_url_to_postid($deliverable_featured);
                                if ($attachment_id) {
                                    $featured_media = wp_get_attachment_image_url($attachment_id, 'card-thumbnail');
                                }
                                // Fallback to original if no thumbnail available
                                if (!$featured_media) {
                                    $featured_media = $deliverable_featured;
                                }
                            } else {
                                // Fallback to first image in media gallery
                                $media = get_field('deliverable_media', $deliverable->ID);
                                if ($media && !empty($media)) {
                                    $first_media = $media[0];
                                    if ($first_media['type'] === 'image') {
                                        $featured_media = $first_media['sizes']['content-medium'] ?? $first_media['sizes']['medium'] ?? $first_media['sizes']['card-thumbnail'] ?? $first_media['url'];
                                    }
                                }
                            }
                        }
                                        
                                        // Get deliverable excerpt
                                        $excerpt = get_field('deliverable_excerpt', $deliverable->ID);
                                        $description = $excerpt ?: get_the_excerpt($deliverable->ID);
                                        
                                        // Get deliverable type for tags
                                        $tags = [];
                                        $type_terms = get_the_terms($deliverable->ID, 'deliverable_type');
                                        if ($type_terms && !is_wp_error($type_terms)) {
                                            $type_term = $type_terms[0];
                                            $tags[] = get_singular_term_display_name($type_term->name);
                                        }
                                        
                                                                // Use master card system for project deliverables
                        ee_render_deliverable_card($deliverable->ID, 'single', [
                            'image_url' => $featured_media,
                            'image_alt' => $deliverable->post_title,
                            'title' => $deliverable->post_title,
                            'description' => $description,
                            'tags' => $tags
                        ]);
                                    endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </article>

                    <?php 
                    // Include the flexible sidebar
                    get_template_part('partials/project-sidebar', null, [
                        'context' => 'project',
                        'config' => [
                            'show_meta' => true,
                            'show_tags' => true,
                            'show_related' => true,
                            'related_count' => 5,
                            'sidebar_class' => 'project-layout-sidebar'
                        ]
                    ]); 
                    ?>
                </div>
            </div>
        </section>
    <?php endwhile; else : ?>
        <section class="no-content">
            <div class="container">
                <p class="no-content__text text-gray-700">No project found.</p>
            </div>
        </section>
    <?php endif; ?>

    <?php get_template_part('partials/contact'); ?>
</main>

<?php get_footer(); ?> 