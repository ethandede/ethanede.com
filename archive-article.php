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
        echo '<div class="single-page-tag-wrapper">';
        echo '<span class="single-page-tag tag-category">';
        echo '<i class="fa-duotone fa-folder"></i> Category';
        echo '</span>';
        echo '</div>';
        echo '<h1>' . esc_html($term->name) . '</h1>';
        if ($term->description) {
          echo '<p class="supporting-text">' . esc_html($term->description) . '</p>';
        }
      } elseif (is_tax('article_tag')) {
        $term = get_queried_object();
        echo '<div class="single-page-tag-wrapper">';
        echo '<span class="single-page-tag tag-tag">';
        echo '<i class="fa-duotone fa-tag"></i> Tag';
        echo '</span>';
        echo '</div>';
        echo '<h1>' . esc_html($term->name) . '</h1>';
        echo '<p class="supporting-text">Articles tagged with "' . esc_html($term->name) . '"</p>';
      } else {
        echo '<div class="single-page-tag-wrapper">';
        echo '<span class="single-page-tag tag-blog">';
        echo '<i class="fa-duotone fa-newspaper"></i> Blog';
        echo '</span>';
        echo '</div>';
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
            <?php while (have_posts()) : the_post();
              get_template_part('partials/card', null, [
                'type' => 'article',
                'context' => 'archive',
                'post_id' => get_the_ID(),
                'extra_classes' => ['card--archive']
              ]);
            endwhile; ?>
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
      
      <?php get_template_part('partials/blog-archive-sidebar'); ?>
    </div>
  </div>
</section>

<!-- Contact Section (Overlay) -->
<?php get_template_part('partials/contact'); ?>

</main>

<?php get_footer(); ?>