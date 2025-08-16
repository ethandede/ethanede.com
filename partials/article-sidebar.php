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
    <h3 class="single-sidebar__title">Related Articles</h3>
    <div class="meta-sidebar">
        <div class="meta-item related-articles-sidebar">
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
                    <div class="related-articles-grid">
                        <?php while ($related_articles->have_posts()) : $related_articles->the_post();
                            get_template_part('partials/card', null, [
                                'type' => 'article',
                                'context' => 'sidebar',
                                'post_id' => get_the_ID(),
                                'extra_classes' => ['card--sidebar']
                            ]);
                        endwhile; ?>
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
    <h3 class="single-sidebar__title">Latest Work</h3>
    <div class="meta-sidebar">
        <div class="meta-item latest-work-sidebar">
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
                    <div class="latest-work-grid">
                        <?php while ($latest_work->have_posts()) : $latest_work->the_post();
                            get_template_part('partials/card', null, [
                                'type' => 'deliverable',
                                'context' => 'sidebar',
                                'post_id' => get_the_ID(),
                                'extra_classes' => ['card--sidebar']
                            ]);
                        endwhile; ?>
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



    <!-- Back to Blog -->
    <div class="meta-sidebar">
        <div class="meta-item">
            <a href="<?php echo get_post_type_archive_link('article'); ?>" class="back-to-blog-link">
                <i class="fa fa-arrow-left"></i> Back to All Articles
            </a>
        </div>
    </div>
</aside>