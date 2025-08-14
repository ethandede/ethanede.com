<?php
/**
 * The template for displaying single articles (blog posts).
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

<!-- Article Header Section -->
<section class="article-header">
  <div class="container">
    <div class="article-header__content">
      <?php
      if (have_posts()) :
        while (have_posts()) : the_post();
          ?>
          <div class="article-meta">
            <time datetime="<?php echo get_the_date('c'); ?>" class="article-date">
              <?php echo get_the_date('F j, Y'); ?>
            </time>
            
            <?php
            $categories = get_the_terms(get_the_ID(), 'article_category');
            if ($categories && !is_wp_error($categories)) :
              ?>
              <span class="article-category">
                <a href="<?php echo get_term_link($categories[0]); ?>">
                  <?php echo esc_html($categories[0]->name); ?>
                </a>
              </span>
            <?php endif; ?>
          </div>
          
          <h1><?php the_title(); ?></h1>
          
          <?php if (has_excerpt()) : ?>
            <p class="article-excerpt"><?php echo get_the_excerpt(); ?></p>
          <?php endif; ?>
          
          <?php
        endwhile;
        rewind_posts();
      endif;
      ?>
    </div>
  </div>
</section>

<!-- Article Content Section -->
<section class="article-layout">
  <div class="container">
    <div class="article-main">
      <?php
      if (have_posts()) :
        while (have_posts()) : the_post();
          ?>
          <article id="post-<?php the_ID(); ?>" <?php post_class('article-content'); ?>>
            <?php if (has_post_thumbnail()) : ?>
              <div class="article-featured-image">
                <?php the_post_thumbnail('large', ['alt' => get_the_title()]); ?>
              </div>
            <?php endif; ?>
            
            <div class="entry-content">
              <?php the_content(); ?>
            </div>
            
            <footer class="article-footer">
              <?php
              $tags = get_the_terms(get_the_ID(), 'article_tag');
              if ($tags && !is_wp_error($tags)) :
                ?>
                <div class="article-tags">
                  <span class="tags-label">Tags:</span>
                  <?php foreach ($tags as $tag) : ?>
                    <a href="<?php echo get_term_link($tag); ?>" class="tag">
                      <?php echo esc_html($tag->name); ?>
                    </a>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
              
              <div class="article-author">
                <div class="author-avatar">
                  <?php echo get_avatar(get_the_author_meta('ID'), 48); ?>
                </div>
                <div class="author-info">
                  <h4 class="author-name"><?php the_author(); ?></h4>
                  <p class="author-bio"><?php echo get_the_author_meta('description'); ?></p>
                </div>
              </div>
            </footer>
          </article>
          <?php
        endwhile;
      else :
        ?>
        <p class="supporting-text"><?php _e('No content found.', 'ethanede'); ?></p>
        <?php
      endif;
      ?>
      
      <!-- Article Navigation -->
      <nav class="article-navigation">
        <?php
        $prev_post = get_previous_post();
        $next_post = get_next_post();
        
        if ($prev_post || $next_post) :
          ?>
          <div class="nav-links">
            <?php if ($prev_post) : ?>
              <div class="nav-previous">
                <a href="<?php echo get_permalink($prev_post->ID); ?>" rel="prev">
                  <span class="nav-subtitle"><i class="fa fa-arrow-left"></i> Previous Article</span>
                  <span class="nav-title"><?php echo get_the_title($prev_post->ID); ?></span>
                </a>
              </div>
            <?php endif; ?>
            
            <?php if ($next_post) : ?>
              <div class="nav-next">
                <a href="<?php echo get_permalink($next_post->ID); ?>" rel="next">
                  <span class="nav-subtitle">Next Article <i class="fa fa-arrow-right"></i></span>
                  <span class="nav-title"><?php echo get_the_title($next_post->ID); ?></span>
                </a>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </nav>
      
      <!-- Related Articles -->
      <section class="related-articles">
        <h2>Related Articles</h2>
        <div class="related-articles-grid">
          <?php
          // Get related articles based on categories
          $current_categories = wp_get_post_terms(get_the_ID(), 'article_category', ['fields' => 'ids']);
          
          if (!empty($current_categories)) {
            $related_articles = new WP_Query([
              'post_type' => 'article',
              'posts_per_page' => 3,
              'post_status' => 'publish',
              'post__not_in' => [get_the_ID()],
              'tax_query' => [
                [
                  'taxonomy' => 'article_category',
                  'field' => 'term_id',
                  'terms' => $current_categories,
                  'operator' => 'IN'
                ]
              ]
            ]);
            
            if ($related_articles->have_posts()) :
              while ($related_articles->have_posts()) : $related_articles->the_post();
                ?>
                <article class="related-article-card">
                  <?php if (has_post_thumbnail()) : ?>
                    <div class="related-article__image">
                      <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail('card-thumbnail', ['alt' => get_the_title()]); ?>
                      </a>
                    </div>
                  <?php endif; ?>
                  
                  <div class="related-article__content">
                    <time datetime="<?php echo get_the_date('c'); ?>" class="related-article__date">
                      <?php echo get_the_date('M j, Y'); ?>
                    </time>
                    
                    <h3 class="related-article__title">
                      <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    
                    <?php if (has_excerpt()) : ?>
                      <p class="related-article__excerpt"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                    <?php endif; ?>
                  </div>
                </article>
                <?php
              endwhile;
              wp_reset_postdata();
            else :
              ?>
              <p class="supporting-text">No related articles found.</p>
            <?php endif;
          } else {
            // Fallback to recent articles if no categories
            $recent_articles = new WP_Query([
              'post_type' => 'article',
              'posts_per_page' => 3,
              'post_status' => 'publish',
              'post__not_in' => [get_the_ID()]
            ]);
            
            if ($recent_articles->have_posts()) :
              while ($recent_articles->have_posts()) : $recent_articles->the_post();
                ?>
                <article class="related-article-card">
                  <?php if (has_post_thumbnail()) : ?>
                    <div class="related-article__image">
                      <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail('card-thumbnail', ['alt' => get_the_title()]); ?>
                      </a>
                    </div>
                  <?php endif; ?>
                  
                  <div class="related-article__content">
                    <time datetime="<?php echo get_the_date('c'); ?>" class="related-article__date">
                      <?php echo get_the_date('M j, Y'); ?>
                    </time>
                    
                    <h3 class="related-article__title">
                      <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    
                    <?php if (has_excerpt()) : ?>
                      <p class="related-article__excerpt"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                    <?php endif; ?>
                  </div>
                </article>
                <?php
              endwhile;
              wp_reset_postdata();
            endif;
          }
          ?>
        </div>
      </section>
    </div>
    
    <aside class="article-sidebar">
      <div class="sidebar-widget">
        <h3>Categories</h3>
        <?php
        $categories = get_terms([
          'taxonomy' => 'article_category',
          'hide_empty' => true,
          'orderby' => 'name',
          'order' => 'ASC'
        ]);
        
        if (!empty($categories) && !is_wp_error($categories)) :
          ?>
          <ul class="category-list">
            <?php foreach ($categories as $category) : ?>
              <li>
                <a href="<?php echo get_term_link($category); ?>">
                  <?php echo esc_html($category->name); ?>
                  <span class="count">(<?php echo $category->count; ?>)</span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
      
      <div class="sidebar-widget">
        <h3>Recent Articles</h3>
        <?php
        $recent_articles = new WP_Query([
          'post_type' => 'article',
          'posts_per_page' => 5,
          'post_status' => 'publish',
          'post__not_in' => [get_the_ID()]
        ]);
        
        if ($recent_articles->have_posts()) :
          ?>
          <ul class="recent-articles">
            <?php while ($recent_articles->have_posts()) : $recent_articles->the_post(); ?>
              <li>
                <a href="<?php the_permalink(); ?>">
                  <time datetime="<?php echo get_the_date('c'); ?>">
                    <?php echo get_the_date('M j'); ?>
                  </time>
                  <?php the_title(); ?>
                </a>
              </li>
            <?php endwhile; ?>
          </ul>
          <?php
          wp_reset_postdata();
        endif;
        ?>
      </div>
      
      <div class="sidebar-widget">
        <h3>Back to Blog</h3>
        <a href="<?php echo get_post_type_archive_link('article'); ?>" class="back-to-blog">
          <i class="fa fa-arrow-left"></i> All Articles
        </a>
      </div>
    </aside>
  </div>
</section>

<!-- Contact Section (Overlay) -->
<?php get_template_part('partials/contact'); ?>

</main>

<?php get_footer(); ?>