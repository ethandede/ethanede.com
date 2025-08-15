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
          <!-- Article Label - placeholder for future icon -->
          <div class="single-page-tag-wrapper">
            <span class="single-page-tag tag-article">
              <i class="fa-duotone fa-newspaper"></i> Article
            </span>
          </div>
          
          <h1 class="article-title"><?php the_title(); ?></h1>
          
          <div class="article-meta">
            <time datetime="<?php echo get_the_date('c'); ?>" class="article-date">
              <?php echo get_the_date('F j, Y'); ?>
            </time>
            
            <?php
            $categories = get_the_terms(get_the_ID(), 'article_category');
            if ($categories && !is_wp_error($categories)) :
              ?>
              <span class="article-category">
                in <a href="<?php echo get_term_link($categories[0]); ?>">
                  <?php echo esc_html($categories[0]->name); ?>
                </a>
              </span>
            <?php endif; ?>
          </div>
          
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
<section class="single-layout">
  <div class="container">
    <div class="single-content-wrapper">
      <article class="single-main article-main">
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
      </article>
      
      <?php get_template_part('partials/article-sidebar'); ?>
    </div>
  </div>
</section>

<!-- Contact Section (Overlay) -->
<?php get_template_part('partials/contact'); ?>

</main>

<?php get_footer(); ?>