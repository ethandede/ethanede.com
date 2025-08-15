<?php
/**
 * Article/Blog sidebar
 * Used by single-article.php
 * 
 * @package ethanede
 */

// Get the current article ID
$current_article_id = get_the_ID();
?>

<aside class="article-sidebar">
    <!-- Related Articles Section -->
    <div class="meta-sidebar">
        <div class="meta-item related-articles-sidebar">
            <h6>Related Articles</h6>
            <div class="meta-content">
                <?php
                // Get related articles based on categories
                $current_categories = wp_get_post_terms($current_article_id, 'article_category', ['fields' => 'ids']);
                
                if (!empty($current_categories)) {
                    $related_articles = new WP_Query([
                        'post_type' => 'article',
                        'posts_per_page' => 3,
                        'post_status' => 'publish',
                        'post__not_in' => [$current_article_id],
                        'tax_query' => [
                            [
                                'taxonomy' => 'article_category',
                                'field' => 'term_id',
                                'terms' => $current_categories,
                                'operator' => 'IN'
                            ]
                        ]
                    ]);
                } else {
                    // Fallback to recent articles if no categories
                    $related_articles = new WP_Query([
                        'post_type' => 'article',
                        'posts_per_page' => 3,
                        'post_status' => 'publish',
                        'post__not_in' => [$current_article_id]
                    ]);
                }
                
                if ($related_articles->have_posts()) :
                    ?>
                    <div class="related-articles-list">
                        <?php while ($related_articles->have_posts()) : $related_articles->the_post(); ?>
                            <article class="related-article-item">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="related-article-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('thumbnail', ['alt' => get_the_title()]); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="related-article-info">
                                    <h4 class="related-article-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h4>
                                    <time datetime="<?php echo get_the_date('c'); ?>" class="related-article-date">
                                        <?php echo get_the_date('M j, Y'); ?>
                                    </time>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>
                    <?php
                    wp_reset_postdata();
                else :
                    ?>
                    <p class="no-related">No related articles found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Latest Work Section -->
    <div class="meta-sidebar">
        <div class="meta-item latest-work-sidebar">
            <h6>Latest Work</h6>
            <div class="meta-content">
                <?php
                // Get latest deliverables
                $latest_work = new WP_Query([
                    'post_type' => 'deliverable',
                    'posts_per_page' => 3,
                    'post_status' => 'publish',
                    'orderby' => 'date',
                    'order' => 'DESC'
                ]);
                
                if ($latest_work->have_posts()) :
                    ?>
                    <div class="latest-work-list">
                        <?php while ($latest_work->have_posts()) : $latest_work->the_post(); ?>
                            <article class="latest-work-item">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="latest-work-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('thumbnail', ['alt' => get_the_title()]); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="latest-work-info">
                                    <h4 class="latest-work-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h4>
                                    <?php
                                    // Get deliverable type
                                    $types = get_the_terms(get_the_ID(), 'deliverable_type');
                                    if ($types && !is_wp_error($types)) :
                                        $type = $types[0];
                                        ?>
                                        <span class="latest-work-type"><?php echo esc_html(get_singular_term_display_name($type->name)); ?></span>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>
                    <?php
                    wp_reset_postdata();
                else :
                    ?>
                    <p class="no-work">No recent work found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <div class="tags-sidebar tags-accordion">
        <h6 class="accordion-trigger" data-target="categories-accordion">
            Categories
            <i class="accordion-icon fa-solid fa-plus"></i>
        </h6>
        <div class="accordion-content" id="categories-accordion">
            <?php
            $categories = get_terms([
                'taxonomy' => 'article_category',
                'hide_empty' => true,
                'orderby' => 'count',
                'order' => 'DESC'
            ]);
            
            if (!empty($categories) && !is_wp_error($categories)) :
                ?>
                <div class="tags-cloud">
                    <?php foreach ($categories as $category) : ?>
                        <a href="<?php echo get_term_link($category); ?>" class="tag tag-style-sidebar tag-article-category">
                            <?php echo esc_html($category->name); ?>
                            <span class="tag-count">(<?php echo $category->count; ?>)</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tags Section -->
    <div class="tags-sidebar tags-accordion">
        <h6 class="accordion-trigger" data-target="tags-accordion">
            Popular Tags
            <i class="accordion-icon fa-solid fa-plus"></i>
        </h6>
        <div class="accordion-content" id="tags-accordion">
            <?php
            $tags = get_terms([
                'taxonomy' => 'article_tag',
                'hide_empty' => true,
                'orderby' => 'count',
                'order' => 'DESC',
                'number' => 15
            ]);
            
            if (!empty($tags) && !is_wp_error($tags)) :
                ?>
                <div class="tags-cloud">
                    <?php foreach ($tags as $tag) : ?>
                        <a href="<?php echo get_term_link($tag); ?>" class="tag tag-style-sidebar tag-article-tag">
                            <?php echo esc_html($tag->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Back to Blog -->
    <div class="meta-sidebar">
        <div class="meta-item">
            <a href="<?php echo get_post_type_archive_link('article'); ?>" class="back-to-blog-link">
                <i class="fa fa-arrow-left"></i> Back to All Articles
            </a>
        </div>
    </div>
</aside>