<?php 
// Debug information
error_log('Single Deliverable Template Loaded');
error_log('Post ID: ' . get_the_ID());
error_log('Post Type: ' . get_post_type());
error_log('Post Status: ' . get_post_status());

get_header(); ?>

<main id="deliverable">
    <?php while (have_posts()) : the_post(); ?>
        <section class="deliverable-header">
            <div class="container">
                <h1><?php the_title(); ?></h1>
            </div>
        </section>

        <section class="deliverable-content">
            <div class="container">
                <div class="deliverable-main">
                    <div class="deliverable-description">
                        <?php the_field('deliverable_description'); ?>
                    </div>

                    <?php
                    // Display deliverable media gallery
                    $media = get_field('deliverable_media');
                    if ($media) : ?>
                        <div class="deliverable-gallery">
                            <?php foreach ($media as $index => $item) : ?>
                                <div class="gallery-item" data-index="<?php echo $index; ?>">
                                    <?php if ($item['type'] === 'image') : ?>
                                        <img src="<?php echo esc_url($item['url']); ?>" 
                                             alt="<?php echo esc_attr($item['alt']); ?>"
                                             class="gallery-image">
                                    <?php elseif ($item['type'] === 'video') : ?>
                                        <video controls class="gallery-video">
                                            <source src="<?php echo esc_url($item['url']); ?>" 
                                                    type="<?php echo esc_attr($item['mime_type']); ?>">
                                        </video>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Gallery Overlay -->
                        <div id="gallery-overlay" class="gallery-overlay">
                            <div class="gallery-overlay-content">
                                <button class="gallery-close">&times;</button>
                                <button class="gallery-prev">&lt;</button>
                                <button class="gallery-next">&gt;</button>
                                <div class="gallery-carousel">
                                    <?php foreach ($media as $item) : ?>
                                        <div class="carousel-item">
                                            <?php if ($item['type'] === 'image') : ?>
                                                <img src="<?php echo esc_url($item['url']); ?>" 
                                                     alt="<?php echo esc_attr($item['alt']); ?>">
                                            <?php elseif ($item['type'] === 'video') : ?>
                                                <video controls>
                                                    <source src="<?php echo esc_url($item['url']); ?>" 
                                                            type="<?php echo esc_attr($item['mime_type']); ?>">
                                                </video>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <aside class="deliverable-sidebar">
                    <div class="deliverable-meta-sidebar">
                        <?php
                        // Get related project
                        $related_project = get_field('related_project');
                        if ($related_project && is_array($related_project) && !empty($related_project)) :
                            $project_id = $related_project[0];
                            $project = get_post($project_id);
                            if ($project && !is_wp_error($project)) :
                            ?>
                            <div class="meta-item">
                                <h6>Related Project</h6>
                                <div class="meta-content">
                                    <span><?php echo esc_html($project->post_title); ?></span>
                                </div>
                            </div>
                        <?php 
                            endif;
                        endif; ?>

                        <?php
                        // Get deliverable type
                        $type = get_field('deliverable_type');
                        if ($type) :
                            $type_term = get_term($type, 'deliverable_type');
                            ?>
                            <div class="meta-item">
                                <h6>Type</h6>
                                <div class="meta-content">
                                    <span><?php echo esc_html($type_term->name); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="meta-item">
                            <h6>Company</h6>
                            <div class="meta-content">
                                <?php 
                                $company_id = get_field('deliverable_company');
                                if ($company_id) {
                                    $company = get_term($company_id, 'company');
                                    if ($company && !is_wp_error($company)) {
                                        echo '<span>' . esc_html($company->name) . '</span>';
                                    }
                                }
                                ?>
                            </div>
                        </div>

                        <?php
                        // Get deliverable status
                        $status = get_field('deliverable_status');
                        if ($status) :
                            $status_term = get_term($status, 'deliverable_status');
                            ?>
                            <div class="meta-item">
                                <h6>Status</h6>
                                <div class="meta-content">
                                    <span><?php echo esc_html($status_term->name); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php
                    // Display tags from taxonomies
                    $technologies = get_field('deliverable_technologies');
                    $skills = get_field('deliverable_skills');
                    $type = get_field('deliverable_type');
                    $all_tags = [];

                    // Add technologies
                    if ($technologies) {
                        foreach ($technologies as $tech_id) {
                            $tech = get_term($tech_id, 'technology');
                            if ($tech && !is_wp_error($tech)) {
                                $all_tags[] = [
                                    'name' => $tech->name,
                                    'link' => get_term_link($tech),
                                    'type' => 'technology'
                                ];
                            }
                        }
                    }

                    // Add skills
                    if ($skills) {
                        foreach ($skills as $skill_id) {
                            $skill = get_term($skill_id, 'skill');
                            if ($skill && !is_wp_error($skill)) {
                                $all_tags[] = [
                                    'name' => $skill->name,
                                    'link' => get_term_link($skill),
                                    'type' => 'skill'
                                ];
                            }
                        }
                    }

                    // Add type
                    if ($type) {
                        $type_term = get_term($type, 'deliverable_type');
                        if ($type_term && !is_wp_error($type_term)) {
                            $all_tags[] = [
                                'name' => $type_term->name,
                                'link' => get_term_link($type_term),
                                'type' => 'type'
                            ];
                        }
                    }

                    if (!empty($all_tags)) : ?>
                        <div class="deliverable-tags-sidebar">
                            <h6>Tags</h6>
                            <div class="tags-cloud">
                                <?php foreach ($all_tags as $tag) : ?>
                                    <a href="<?php echo esc_url($tag['link']); ?>" 
                                       class="tag tag-<?php echo esc_attr($tag['type']); ?>">
                                        <?php echo esc_html($tag['name']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="related-deliverables">
                        <h3>Related Deliverables</h3>
                        <?php
                        // Get related deliverables based on taxonomies
                        $related_args = [
                            'post_type' => 'deliverable',
                            'posts_per_page' => 3,
                            'post__not_in' => [get_the_ID()],
                            'tax_query' => [
                                'relation' => 'OR',
                                [
                                    'taxonomy' => 'technology',
                                    'field' => 'term_id',
                                    'terms' => $technologies,
                                ],
                                [
                                    'taxonomy' => 'skill',
                                    'field' => 'term_id',
                                    'terms' => $skills,
                                ],
                                [
                                    'taxonomy' => 'deliverable_type',
                                    'field' => 'term_id',
                                    'terms' => [$type],
                                ],
                            ],
                        ];
                        $related_query = new WP_Query($related_args);

                        if ($related_query->have_posts()) :
                            while ($related_query->have_posts()) : $related_query->the_post(); ?>
                                <a href="<?php the_permalink(); ?>" class="related-deliverable">
                                    <h4><?php the_title(); ?></h4>
                                    <?php
                                    $type = get_field('deliverable_type');
                                    if ($type) :
                                        $type_term = get_term($type, 'deliverable_type');
                                        echo '<span class="type">' . esc_html($type_term->name) . '</span>';
                                    endif;
                                    ?>
                                </a>
                            <?php endwhile;
                            wp_reset_postdata();
                        endif; ?>
                    </div>
                </aside>
            </div>
        </section>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?> 