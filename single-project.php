<?php
/**
 * Template for displaying single project posts.
 *
 * @package ethanede
 */

get_header();
?>

<!-- Background Animation Container -->
<div class="background-animation">
  <svg class="animated-squares" viewBox="0 0 1920 1080" preserveAspectRatio="xMidYMid slice"></svg>
</div>

<!-- Navigation -->
<nav class="site-nav">
    <div class="container">
      <div class="nav-content">
        <h3 class="nav-title"><a href="<?php echo home_url(); ?>">Ethan Ede</a></h3>
        <button class="hamburger" aria-label="Toggle mobile menu">
          <span class="bar top"></span>
          <span class="bar middle"></span>
          <span class="bar bottom"></span>
        </button>
        <ul class="nav-links">
          <li><a href="<?php echo home_url(); ?>#about">About</a></li>
          <li><a href="<?php echo home_url(); ?>#skills">Skills & Experience</a></li>
          <li><a href="<?php echo home_url(); ?>#contact">Contact</a></li>
        </ul>
      </div>
    </div>
</nav>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu">
  <ul class="mobile-nav-links">
    <li><a href="<?php echo home_url(); ?>">Home</a></li>
    <li><a href="<?php echo home_url(); ?>#about">About</a></li>
    <li><a href="<?php echo home_url(); ?>#skills">Skills & Experience</a></li>
    <li><a href="<?php echo home_url(); ?>#contact" class="contact-trigger">Contact</a></li>
  </ul>
</div>

<!-- Color controls UI -->
<?php get_template_part('partials/color-controls'); ?>

<main class="main single-project">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <div class="hero__content">
                    <h1 class="hero__title"><?php echo esc_html(get_field('project_title')); ?></h1>
                    <?php if ($company_name = get_field('company_name')) : ?>
                        <h2 class="hero__subtitle"><?php echo esc_html($company_name); ?></h2>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Project Content -->
        <section class="single-layout">
            <div class="container">
                <article class="single-main">
                    <?php if ($featured_media = get_field('featured_media')) : ?>
                        <div class="project-featured-media" style="margin-bottom: 2rem;">
                            <?php
                            $file_type = wp_check_filetype($featured_media);
                            if (strpos($file_type['type'], 'video') !== false) : ?>
                                <video class="project-featured-video" controls style="width: 100%; max-width: 800px; height: auto; display: block; margin: 0 auto; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                                    <source src="<?php echo esc_url($featured_media); ?>" type="<?php echo esc_attr($file_type['type']); ?>">
                                    Your browser does not support the video tag.
                                </video>
                            <?php else : ?>
                                <img src="<?php echo esc_url($featured_media); ?>" 
                                     alt="<?php echo esc_attr(get_field('project_title')); ?>" 
                                     class="project-featured-image"
                                     style="width: 100%; max-width: 800px; height: auto; display: block; margin: 0 auto; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="project-header">
                        <?php if ($company_logo = get_field('company_logo')) : ?>
                            <div class="company-logo">
                                <img src="<?php echo esc_url($company_logo['url']); ?>" 
                                     alt="<?php echo esc_attr($company_logo['alt']); ?>" 
                                     class="company-logo__image">
                            </div>
                        <?php endif; ?>

                        <?php if ($company_description = get_field('company_description')) : ?>
                            <div class="company-description">
                                <?php echo wp_kses_post($company_description); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="project-description">
                        <?php echo wp_kses_post(get_field('role_description')); ?>
                    </div>

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

                    <?php if ($deliverables = get_field('deliverables')) : ?>
                        <div class="project-deliverables">
                            <h3>Deliverables</h3>
                            <div class="deliverables-grid">
                                <?php foreach ($deliverables as $deliverable) : 
                                    $type = $deliverable['deliverable_type'];
                                    switch ($type) :
                                        case 'Image' :
                                        case 'Video' :
                                            if ($file = $deliverable['deliverable_file']) : ?>
                                                <div class="deliverable-item">
                                                    <?php if ($type === 'Video') : ?>
                                                        <video controls>
                                                            <source src="<?php echo esc_url($file['url']); ?>" type="<?php echo esc_attr($file['mime_type']); ?>">
                                                            Your browser does not support the video tag.
                                                        </video>
                                                    <?php else : ?>
                                                        <img src="<?php echo esc_url($file['url']); ?>" 
                                                             alt="<?php echo esc_attr($file['title']); ?>">
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif;
                                            break;
                                        case 'PDF' :
                                            if ($pdf = $deliverable['deliverable_pdf']) : ?>
                                                <div class="deliverable-item">
                                                    <a href="<?php echo esc_url($pdf['url']); ?>" target="_blank" rel="noopener noreferrer">
                                                        <i class="fas fa-file-pdf"></i>
                                                        <?php echo esc_html($pdf['title']); ?>
                                                    </a>
                                                </div>
                                            <?php endif;
                                            break;
                                        case 'Link' :
                                            if ($url = $deliverable['deliverable_url']) : ?>
                                                <div class="deliverable-item">
                                                    <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer">
                                                        <i class="fas fa-external-link-alt"></i>
                                                        <?php echo esc_url($url); ?>
                                                    </a>
                                                </div>
                                            <?php endif;
                                            break;
                                    endswitch;
                                endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="project-meta">
                        <?php
                        // Display Project Categories
                        $categories = get_the_terms(get_the_ID(), 'project_category');
                        if ($categories && !is_wp_error($categories)) : ?>
                            <div class="project-meta__item project-categories">
                                <h3 class="project-meta__title">Categories</h3>
                                <div class="project-meta__terms">
                                    <?php foreach ($categories as $category) : ?>
                                        <span class="project-meta__term"><?php echo esc_html($category->name); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php
                        // Display Project Tags
                        $tags = get_the_terms(get_the_ID(), 'project_tag');
                        if ($tags && !is_wp_error($tags)) : ?>
                            <div class="project-meta__item project-tags">
                                <h3 class="project-meta__title">Tags</h3>
                                <div class="project-meta__terms">
                                    <?php foreach ($tags as $tag) : ?>
                                        <span class="project-meta__term"><?php echo esc_html($tag->name); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>
            </div>
        </section>
    <?php endwhile; else : ?>
        <section class="no-content">
            <div class="container">
                <p class="no-content__text">No project found.</p>
            </div>
        </section>
    <?php endif; ?>

    <?php get_template_part('partials/contact'); ?>
</main>

<?php get_footer(); ?> 