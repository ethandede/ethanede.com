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

<main id="home">

<!-- Navigation -->
<nav class="site-nav">
    <div class="container">
      <div class="nav-content">
        <h3 class="nav-title"><a href="#home">Ethan Ede</a></h3>
        <button class="hamburger" aria-label="Toggle mobile menu">
          <span class="bar top"></span>
          <span class="bar middle"></span>
          <span class="bar bottom"></span>
        </button>
        <ul class="nav-links">
          <li><a href="#about">About</a></li>
          <li><a href="#skills">Skills & Experience</a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Persistent CTA -->
  <div class="persistent-cta">
    <div class="container">
      <a href="#contact" class="cta-button contact-trigger">Let's work together <i class="fa fa-arrow-right"></i></a>
    </div>
  </div>
  
  <!-- Mobile Menu Overlay -->
  <div class="mobile-menu">
    <ul class="mobile-nav-links">
      <li><a href="#home">Home</a></li>
      <li><a href="#about">About</a></li>
      <li><a href="#skills">Skills & Experience</a></li>
      <li><a href="#contact">Contact</a></li>
    </ul>
  </div>

  <!-- Color controls UI -->
  <?php get_template_part('partials/color-controls'); ?>

  <!-- Hero Section without the SVG -->
  <section class="hero">
    <div class="container">
      <h4>Welcome.</h4>
      <h1>
        Let's build a website <br>that
        <span class=rotating-word-break><br></span>
        <span class="rotating-word"></span>
      </h1>
      <p class="supporting-text">I'm a digital strategist with over 20 years of experience blending technology, creativity, and marketing to build websites that deliver results. My passion is collaborating with teams to craft digital experiences that engage users and drive growth, powered by modern tools and AI innovation.</p>
      <a href="/contact" class="hero-button cta-button">Let's build something together <i class="fa fa-arrow-right"></i></a>
    </div>
  </section>

  <!-- Things I Do Section -->
  <section class="what-i-do" id="about">
    <div class="container">
      <h2>What I do</h2>
      <p class="supporting-text">I partner with businesses and creative teams to create digital solutions that align with their vision and goals. By combining strategic insight, intuitive design, and technical expertise, I help deliver experiences that resonate with audiences and achieve measurable outcomes.</p>
      <div class="grid">
        <div class="item">
          <h4>Strategy + Vision</h4>
          <p>Working closely with stakeholders, I help transform complex challenges into clear, actionable plans that align business objectives with emerging opportunities.</p>
        </div>
        <div class="item">
          <h4>Design + Experience</h4>
          <p>Collaborating with designers and developers, I craft intuitive, user-focused digital experiences that balance aesthetics with seamless functionality.</p>
        </div>
        <div class="item">
          <h4>Development + Engineering</h4>
          <p>I work alongside engineering teams to build robust, scalable solutions using modern technologies, ensuring innovative ideas come to life reliably.</p>
        </div>
        <div class="item">
          <h4>Marketing + Analytics</h4>
          <p>Partnering with marketing teams, I develop data-driven campaigns that connect brands with their audiences and deliver actionable insights for growth.</p>
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
        // Sample Portfolio Items with Picsum Placeholder Images
        $portfolio_items = [
          [
            "image" => get_template_directory_uri() . "/assets/img/image_websiteManagement.png",
            "title" => "Website Management",
            "description" => "Partnering with teams to ensure websites are fast, reliable, and user-friendly, creating digital journeys that are elegant and a joy to experience.",
            "tags" => ["Development", "UX Design", "Strategy"],
            "link" => "#"
          ],
          [
            "image" => get_template_directory_uri() . "/assets/img/image_websiteDevelopment.png",
            "title" => "Website Development",
            "description" => "Collaborating with developers and designers to build websites that are intuitive, accessible, and high-performing, delivering seamless and engaging digital experiences for all users.",
            "tags" => ["UX Design", "Analytics", "Reporting"],
            "link" => "#"
          ],
          [
            "image" => get_template_directory_uri() . "/assets/img/image_seoCRO.png",
            "title" => "SEO and CRO",
            "description" => "Working closely with marketing teams to optimize digital presence and boost conversions, using data-driven strategies that connect with users and achieve goals.",
            "tags" => ["Analytics", "Strategy", "Development"],
            "link" => "#"
          ],
          [
            "image" => get_template_directory_uri() . "/assets/img/image_analyticsReporting.png",
            "title" => "Analytics and Reporting",
            "description" => "Partnering with data experts to transform complex datasets into clear, actionable insights, empowering businesses to make smart decisions with confidence and clarity.",
            "tags" => ["Development", "Strategy", "E-Commerce"],
            "link" => "#"
          ],
          [
            "image" => get_template_directory_uri() . "/assets/img/image_uxUiDesign.png",
            "title" => "UX/UI Design",
            "description" => "Collaborating with creative teams to craft interfaces that are visually stunning and easy to use, designing experiences that prioritize users at every step.",
            "tags" => ["Marketing", "Analytics", "A/B Testing"],
            "link" => "#"
          ],
          [
            "image" => get_template_directory_uri() . "/assets/img/image_strategyVision.png",
            "title" => "Strategy and Vision",
            "description" => "Working hand-in-hand with stakeholders to shape innovative digital strategies that align with business objectives, paving the way for long-term growth and success.",
            "tags" => ["Reporting", "Data Analysis", "Strategy"],
            "link" => "#"
          ],
        ];

        // Loop through and display portfolio items
        foreach ($portfolio_items as $item): ?>
          <!-- Wrap the entire card in an anchor to make it clickable -->
          <a class="portfolio-link" href="<?php echo $item['link']; ?>">
            <div class="portfolio-item">
              <!-- NEW: Overlay with a search icon -->
              <div class="portfolio-overlay">
              </div>

              <div class="portfolio-tags">
                <?php foreach ($item['tags'] as $tag): ?>
                  <span class="tag"><?php echo $tag; ?></span>
                <?php endforeach; ?>
              </div>
              <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['title']; ?>">
              <div class="portfolio-copy">
                <h3><?php echo $item['title']; ?></h3>
                <p><?php echo $item['description']; ?></p>
              </div>
              <div class="portfolio-arrow">
                <i class="fas fa-arrow-right"></i>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- AI Tools Section -->
  <section class="ai-tools">
    <div class="container">
      <h2>AI tools and technologies</h2>
      <p class="supporting-text">I leverage cutting-edge AI to amplify creativity, efficiency, and impact in digital
        solutions.</p>
      <div class="grid">
        <div class="item">
          <h4>ChatGPT</h4>
          <p>Automating content creation and ideation—generated SEO-optimized posts for [Company Z], cutting time by
            30%.</p>
        </div>
        <div class="item">
          <h4>Grok</h4>
          <p>Analyzing data and user feedback—prioritized features for [Company V] with actionable insights.</p>
        </div>
        <div class="item">
          <h4>Stable Diffusion</h4>
          <p>Prototyping visuals—designed unique assets for [Project W], streamlining workflows.</p>
        </div>
        <div class="item">
          <h4>Midjourney</h4>
          <p>Enhancing creative output—crafted concept art for [Project X], reducing design time by 50%.</p>
        </div>
      </div>
    </div>
  </section>

<!-- Contact Section (Overlay) -->
<section class="contact-section" id="contact">
    <div class="contact-overlay">
      <div class="container">
        <div class="contact-form-container">
          <header class="contact-header">
            <h2>Get in Touch</h2>
            <p>Please fill out the form below and I'll get back to you as soon as possible.</p>
            <button class="contact-close">×</button>
          </header>
          <?php echo do_shortcode('[contact-form-7 id="eb95201" title="Contact Form - Ethan Ede"]'); ?>
        </div>
      </div>
    </div>
  </section>

</main>

<?php get_footer(); ?>