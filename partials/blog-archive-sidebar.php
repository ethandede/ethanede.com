<?php
/**
 * Blog Archive sidebar
 * Used by archive-article.php
 * 
 * @package ethanede
 */
?>

<aside class="blog-sidebar">
    <!-- Categories Section -->
    <h3 class="single-sidebar__title">Categories</h3>
    <div class="tags-sidebar">
        <div class="tags-cloud">
            <?php
            $categories = get_terms([
                'taxonomy' => 'article_category',
                'hide_empty' => true,
                'orderby' => 'count',
                'order' => 'DESC'
            ]);
            
            if (!empty($categories) && !is_wp_error($categories)) :
                foreach ($categories as $category) : ?>
                    <a href="<?php echo get_term_link($category); ?>" class="tag tag-style-sidebar tag-article-category">
                        <?php echo esc_html($category->name); ?>
                        <span class="tag-count">(<?php echo $category->count; ?>)</span>
                    </a>
                <?php endforeach;
            endif; ?>
        </div>
    </div>

    <!-- Recent Articles Section -->
    <h3 class="single-sidebar__title">Recent Articles</h3>
    <div class="meta-sidebar">
        <div class="meta-item recent-articles-sidebar">
            <div class="meta-content">
                <?php
                $recent_articles = new WP_Query([
                    'post_type' => 'article',
                    'posts_per_page' => 5,
                    'post_status' => 'publish',
                    'post__not_in' => [get_the_ID()]
                ]);
                
                if ($recent_articles->have_posts()) :
                    ?>
                    <div class="recent-articles-grid">
                        <?php while ($recent_articles->have_posts()) : $recent_articles->the_post();
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
                    <p class="no-recent">No recent articles found.</p>
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

    <!-- Popular Tags Section -->
    <h3 class="single-sidebar__title">Popular Tags</h3>
    <div class="tags-sidebar">
        <div class="tags-cloud">
            <?php
            $tags = get_terms([
                'taxonomy' => 'article_tag',
                'hide_empty' => true,
                'orderby' => 'count',
                'order' => 'DESC',
                'number' => 15
            ]);
            
            if (!empty($tags) && !is_wp_error($tags)) :
                foreach ($tags as $tag) : ?>
                    <a href="<?php echo get_term_link($tag); ?>" class="tag tag-style-sidebar tag-article-tag">
                        <?php echo esc_html($tag->name); ?>
                    </a>
                <?php endforeach;
            endif; ?>
        </div>
    </div>
</aside>