<?php
/**
 * The template for displaying article archives (blog).
 *
 * @package ethanede
 */

// Set custom page title for blog archive
add_filter('wp_title', function($title) {
    if (is_tax('article_category')) {
        $term = get_queried_object();
        return $term->name . ' Articles | ' . get_bloginfo('name');
    } elseif (is_tax('article_tag')) {
        $term = get_queried_object();
        return 'Articles tagged "' . $term->name . '" | ' . get_bloginfo('name');
    } else {
        return 'Blog - Articles & Insights | ' . get_bloginfo('name');
    }
});

// Set custom meta description
add_action('wp_head', function() {
    if (is_tax('article_category')) {
        $term = get_queried_object();
        $description = $term->description ?: 'Articles in the ' . $term->name . ' category.';
        echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    } elseif (is_tax('article_tag')) {
        $term = get_queried_object();
        echo '<meta name="description" content="Articles tagged with ' . esc_attr($term->name) . '.">' . "\n";
    } else {
        echo '<meta name="description" content="Read my latest articles and insights on web development, technology, and digital solutions.">' . "\n";
    }
});

get_header();
?>

<!-- Persistent CTA -->
<div class="persistent-cta">
  <div class="container">
    <a href="#contact" class="cta-button contact-trigger">Let's work together <i class="fa fa-arrow-right"></i></a>
  </div>
</div>

<!-- Blog Header Section -->
<section class="blog-header">
  <div class="container">
    <div class="blog-header__content">
      <?php
      if (is_tax('article_category')) {
        $term = get_queried_object();
        echo '<span class="page-tag">Category</span>';
        echo '<h1>' . esc_html($term->name) . '</h1>';
        if ($term->description) {
          echo '<p class="supporting-text">' . esc_html($term->description) . '</p>';
        }
      } elseif (is_tax('article_tag')) {
        $term = get_queried_object();
        echo '<span class="page-tag">Tag</span>';
        echo '<h1>' . esc_html($term->name) . '</h1>';
        echo '<p class="supporting-text">Articles tagged with "' . esc_html($term->name) . '"</p>';
      } else {
        echo '<span class="page-tag">Blog</span>';
        echo '<h1>Articles & Insights</h1>';
        echo '<p class="supporting-text">Thoughts on web development, technology, and digital solutions.</p>';
      }
      ?>
    </div>
  </div>
</section>

<!-- Articles Grid Section -->
<section class="blog-content">
  <div class="container">
    <div class="blog-layout">
      <main class="blog-main">
        <?php if (have_posts()) : ?>
          <div class="articles-grid">
            <?php while (have_posts()) : the_post(); ?>
              <article class="article-card">
                <?php if (has_post_thumbnail()) : ?>
                  <div class="article-card__image">
                    <a href="<?php the_permalink(); ?>">
                      <?php the_post_thumbnail('card-thumbnail', ['alt' => get_the_title()]); ?>
                    </a>
                  </div>
                <?php endif; ?>
                
                <div class="article-card__content">
                  <div class="article-card__meta">
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
                  
                  <h2 class="article-card__title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                  </h2>
                  
                  <?php if (has_excerpt()) : ?>
                    <p class="article-card__excerpt"><?php echo get_the_excerpt(); ?></p>
                  <?php endif; ?>
                  
                  <a href="<?php the_permalink(); ?>" class="article-card__link">
                    Read more <i class="fa fa-arrow-right"></i>
                  </a>
                </div>
              </article>
            <?php endwhile; ?>
          </div>
          
          <?php
          // Pagination
          $pagination = paginate_links([
            'prev_text' => '<i class="fa fa-arrow-left"></i> Previous',
            'next_text' => 'Next <i class="fa fa-arrow-right"></i>'
          ]);
          
          if ($pagination) :
            ?>
            <nav class="blog-pagination">
              <?php echo $pagination; ?>
            </nav>
          <?php endif; ?>
          
        <?php else : ?>
          <div class="no-articles">
            <h2>No articles found</h2>
            <p class="supporting-text">
              <?php if (is_tax()) : ?>
                No articles found in this category or tag.
              <?php else : ?>
                No articles have been published yet. Check back soon!
              <?php endif; ?>
            </p>
          </div>
        <?php endif; ?>
      </main>
      
      <aside class="blog-sidebar">
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
      </aside>
    </div>
  </div>
</section>

<!-- Contact Section (Overlay) -->
<?php get_template_part('partials/contact'); ?>

</main>

<?php get_footer(); ?>