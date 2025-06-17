<?php
/**
 * Template Name: Custom Home Page
 * Template Post Type: page
 * Description: Template for Ethan Ede's custom homepage with 20 animated, overlapping squares (3x larger)
 */
get_header();
?>

<!-- Background Animation Container -->
<div class="background-animation">
  <svg class="animated-squares" viewBox="0 0 1920 1080" preserveAspectRatio="xMidYMid slice"></svg>
</div>

<main id="main" class="site-main">
    
    <div class="container">

  <!-- Persistent CTA -->
  <div class="persistent-cta">
    <div class="container">
      <a href="#contact" class="cta-button contact-trigger text-semibold">Let's work together <i class="fa fa-arrow-right"></i></a>
    </div>
  </div>

  <!-- Color controls UI -->
  <?php get_template_part('partials/color-controls'); ?>

  <!-- Hero Section -->
  <section class="hero">
    <div class="container">
      <h4>Welcome.</h4>
      <h1>
        Let's build a website <br>that
        <span class="rotating-word-break"><br></span>
        <span class="rotating-word"></span>
      </h1>
      <p class="supporting-text">I'm a digital strategist with over 20 years of experience blending technology, creativity, and marketing to build web experiences that deliver results.</p>
      <a href="#contact" class="hero-button cta-button text-semibold">Let's work together <i class="fa fa-arrow-right"></i></a>
    </div>
  </section>

  <!-- Things I Do Section -->
  <section class="what-i-do" id="about">
    <div class="container">
      <h2>What I do</h2>
      <p class="supporting-text">My passion is collaborating with teams to craft digital experiences that engage users and drive growth, powered by modern tools and AI innovation. I help deliver experiences that resonate with audiences and achieve measurable outcomes.</p>
      <div class="grid">
        <div class="item">
          <h4 class="text-primary">Strategy + Vision</h4>
          <p>Working closely with clients and stakeholders to help transform complex challenges into clear, actionable plans that align business objectives with emerging opportunities.</p>
        </div>
        <div class="item">
          <h4 class="text-primary">Design + Experience</h4>
          <p>Collaborating with designers and developers to craft intuitive, user-focused digital experiences that balance aesthetics with seamless functionality.</p>
        </div>
        <div class="item">
          <h4 class="text-primary">Development + Engineering</h4>
          <p>Working solo, with contractors, and/or internal teams to build robust, scalable solutions using modern technologies, ensuring innovative ideas come to life reliably.</p>
        </div>
        <div class="item">
          <h4 class="text-primary">Marketing + Analytics</h4>
          <p>Partnering with clients and marketing teams to develop data-driven campaigns that connect brands with their audiences and deliver actionable insights for growth.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Clients Section -->
  <section class="clients">
    <div class="container">
      <h2>Clients I've worked with</h2>
      <p class="supporting-text">I've had the privilege of partnering with industry-leading companies, collaborating with their teams to bring creative and impactful digital solutions to life.</p>
    </div>
    <div class="logo-banner">
      <div class="logo-track">
        <div class="logo-container">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo_lightspeedSystems.svg"
            alt="Lightspeed Systems">
        </div>
        <div class="logo-container">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo_experian.svg" alt="Experian">
        </div>
        <div class="logo-container">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo_staples.svg" alt="Staples">
        </div>
        <div class="logo-container">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo_d-link.svg" alt="D-Link">
        </div>
        <div class="logo-container">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo_laClippers.svg"
            alt="Los Angeles Clippers">
        </div>
        <div class="logo-container">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo_liveops.svg" alt="Liveops">
        </div>
        <div class="logo-container">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo_bosch.svg" alt="Bosch">
        </div>
        <div class="logo-container">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo_actOn.svg" alt="Act-On">
        </div>
        <div class="logo-container">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo_bestBuy.svg" alt="Best Buy">
        </div>
        <div class="logo-container">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo_rehau.svg" alt="Rehau">
        </div>
        <div class="logo-container">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo_toshiba.svg" alt="Toshiba">
        </div>
        <div class="logo-container">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo_quiksilver.svg" alt="Quiksilver">
        </div>
        <div class="logo-container">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo_portlandJapaneseGarden.svg"
            alt="Portland Japanese Garden">
        </div>
        <div class="logo-container">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo_nba.svg" alt="NBA">
        </div>
        <div class="logo-container">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo_kong.svg" alt="Kong, Inc.">
        </div>
      </div>
    </div>
  </section>

  <!-- Portfolio Section -->
  <section class="portfolio" id="skills">
    <div class="container">
      <h2>How I Work</h2>
      <p class="supporting-text">My approach is rooted in collaboration, communication, and a commitment to shared success. I thrive in team environments, bringing clarity, creativity, and technical expertise to every project. Below are examples of how I've worked with teams to solve real-world challenges and deliver results.</p>
      <div class="portfolio-grid">
        <?php
        // Get all project categories
        $categories = get_terms([
          'taxonomy' => 'project_category',
          'hide_empty' => false,
          'orderby' => 'name',
          'order' => 'ASC',
          'fields' => 'all'
        ]);

        if (!empty($categories) && !is_wp_error($categories)) :
          foreach ($categories as $category) :
            // Get the category image from ACF
            $category_image = get_field('category_image', 'project_category_' . $category->term_id);
            
            // Fallback to first project image if no category image is set
            if (!$category_image) {
              $projects = get_posts([
                'post_type' => 'project',
                'posts_per_page' => 1,
                'tax_query' => [
                  [
                    'taxonomy' => 'project_category',
                    'field' => 'term_id',
                    'terms' => $category->term_id,
                  ],
                ],
              ]);

              if (!empty($projects)) {
                $featured_media = get_field('featured_media', $projects[0]->ID);
                if ($featured_media) {
                  $category_image = $featured_media;
                }
              }
            }

            // Find the corresponding post for this category
            $category_post = get_posts([
              'post_type' => 'post',
              'posts_per_page' => 1,
              'title' => $category->name,
              'post_status' => 'publish'
            ]);

            // Get the link - either to the post or the category archive as fallback
            $link = !empty($category_post) ? get_permalink($category_post[0]->ID) : get_term_link($category);
            ?>
            <a class="portfolio-link" href="<?php echo esc_url($link); ?>">
              <div class="portfolio-item">
                <div class="portfolio-overlay"></div>
                <div class="portfolio-tags">
                  <span class="tag"><?php echo esc_html($category->name); ?></span>
                </div>
                <?php if ($category_image) : ?>
                  <img src="<?php echo esc_url($category_image); ?>" alt="<?php echo esc_attr($category->name); ?>">
                <?php endif; ?>
                <div class="portfolio-copy">
                  <h3><?php echo esc_html($category->name); ?></h3>
                  <p><?php echo esc_html($category->description); ?></p>
                </div>
                <div class="portfolio-arrow">
                  <i class="fas fa-arrow-right"></i>
                </div>
              </div>
            </a>
          <?php endforeach;
        endif; ?>
      </div>
    </div>
  </section>

  <!-- Contact Section (Overlay) -->
  <section class="contact-section" id="contact">
    <div class="contact-overlay">
      <div class="container">
        <div class="contact-form-container">
          <header class="contact-header">
            <h3>Get in Touch</h3>
            <p>Please fill out the form below and I'll get back to you as soon as possible.</p>
            <button class="contact-close">×</button>
          </header>
          <?php echo do_shortcode('[contact-form-7 id="eb95201" title="Contact Form - Ethan Ede"]'); ?>
        </div>
      </div>
    </div>
  </section>

</div>
</main>

<?php get_footer(); ?>