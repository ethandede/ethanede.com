<?php
/**
 * Template Name: Custom Home Page
 * Template Post Type: page
 * Description: Template for Ethan Ede's custom homepage with 20 animated, overlapping squares (3x larger)
 */
get_header();
?>

<main id="main" class="site-main">
    
    <div class="container">

  <!-- Persistent CTA -->
  <div class="persistent-cta">
    <div class="container">
      <a href="#contact" class="cta-button contact-trigger text-semibold">Let's work together <i class="fa fa-arrow-right"></i></a>
    </div>
  </div>

  <!-- Hero Section -->
  <section class="hero">
    <div class="container">
      <h4>Welcome.</h4>
      <h1>
        Let's build a website <br>that
        <span class="rotating-word-break"><br></span>
        <span class="rotating-word"></span>
      </h1>
                      <p class="supporting-text">I'm a digital strategist with over 20 years of experience blending tools, creativity, and marketing to build web experiences that deliver results.</p>
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
          <h4 class="text-primary">Development</h4>
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
      <h2>How I work</h2>
      <p class="supporting-text">My approach is rooted in collaboration, communication, and a commitment to shared success. I thrive in team environments, bringing clarity, creativity, and technical expertise to every project. Below are examples of how I've worked with teams to solve real-world challenges and deliver results.</p>
      <div class="portfolio-grid">
        <?php
        // Get all project categories ordered by ACF field_display_order field
        $categories = get_terms([
          'taxonomy' => 'project_category',
          'hide_empty' => false,
          'meta_key' => 'field_display_order',
          'orderby' => 'meta_value_num',
          'order' => 'ASC',
          'fields' => 'all'
        ]);
        
        // Fallback to name ordering if no field_display_order field is set
        if (empty($categories)) {
          $categories = get_terms([
            'taxonomy' => 'project_category',
            'hide_empty' => false,
            'orderby' => 'name',
            'order' => 'ASC',
            'fields' => 'all'
          ]);
        }

        if (!empty($categories) && !is_wp_error($categories)) :
          foreach ($categories as $category) :
            // Get the category image from ACF field on the taxonomy term
            $category_image = get_field('category_image', 'project_category_' . $category->term_id);
            
            // Find the corresponding regular post for linking by slug
            // Try multiple slug variations to find matching post
            $slug_variations = [];
            
            // Standard slug conversion
            $slug_variations[] = sanitize_title($category->name);
            
            // Replace & with "and" - handle different spacing patterns
            if (strpos($category->name, '&') !== false) {
              // Clean the name first to avoid double encoding
              $clean_name = html_entity_decode($category->name, ENT_QUOTES, 'UTF-8');
              $slug_variations[] = sanitize_title(str_replace('&', 'and', $clean_name));
              $slug_variations[] = sanitize_title(str_replace(' & ', ' and ', $clean_name));
              $slug_variations[] = sanitize_title(str_replace(' & ', '-and-', $clean_name));
            }
            
            // Replace + with "and" 
            if (strpos($category->name, '+') !== false) {
              $clean_name = html_entity_decode($category->name, ENT_QUOTES, 'UTF-8');
              $slug_variations[] = sanitize_title(str_replace('+', 'and', $clean_name));
              $slug_variations[] = sanitize_title(str_replace(' + ', ' and ', $clean_name));
              $slug_variations[] = sanitize_title(str_replace(' + ', '-and-', $clean_name));
            }
            
            // Try each slug variation
            $category_post = [];
            foreach ($slug_variations as $slug) {
              if (!empty($slug)) {
                $category_post = get_posts([
                  'post_type' => 'post',
                  'posts_per_page' => 1,
                  'name' => $slug,
                  'post_status' => 'publish'
                ]);
                
                // If found, break out of loop
                if (!empty($category_post)) {
                  break;
                }
              }
            }

            // Get the link - either to the post or the category archive as fallback
            $link = !empty($category_post) ? get_permalink($category_post[0]->ID) : get_term_link($category);
            
            // Use master card system with taxonomy data
            get_template_part('partials/card', null, [
              'type' => 'project',
              'context' => 'home',
              'post_id' => !empty($category_post) ? $category_post[0]->ID : 0, // Use 0 for taxonomy-only cards
              'link_url' => $link,
              'image_url' => $category_image,
              'image_alt' => $category->name,
              'tags' => [$category->name],
              'title' => $category->name,
              'description' => $category->description,
              'show_media_types' => false,
              'arrow_position' => 'bottom-left'
            ]);
          endforeach;
        endif; ?>
      </div>
    </div>
  </section>



</div>
</main>

<?php get_footer(); ?>