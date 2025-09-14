<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-89QXYH3QB8"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());
    gtag('config', 'G-89QXYH3QB8');
  </script>
</head>

<body <?php body_class(); ?>>
  <?php wp_body_open(); ?>

  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9345046210950956"
    crossorigin="anonymous"></script>

  <!-- Background Animation Container -->
  <div class="background-animation">
    <svg class="animated-squares" viewBox="0 0 1920 1080" preserveAspectRatio="xMidYMid slice"></svg>
  </div>

  <header class="site-header">
    <?php get_template_part('partials/site-navigation'); ?>
  </header>

  <!-- Color Controls UI -->
  <?php get_template_part('partials/color-controls'); ?>

  <main id="main" class="site-main">