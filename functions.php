<?php
// Add theme support
function ee_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
}
add_action('after_setup_theme', 'ee_theme_setup');

// Enqueue styles and scripts
function ee_enqueue_assets() {
    // Enqueue your compiled CSS from assets/css
    wp_enqueue_style(
        'ee-style',
        get_template_directory_uri() . '/assets/css/style.css',
        [],
        '1.0.0',
        'all'
    );
}
add_action('wp_enqueue_scripts', 'ee_enqueue_assets');

// Register menus
function ee_register_menus() {
    register_nav_menus([
        'main_navigation' => __('Main Navigation', 'ethanede'),
    ]);
}
add_action('init', 'ee_register_menus');

function register_navwalker(){
    require_once get_template_directory() . '/includes/class-wp-bootstrap-navwalker.php';
}
add_action( 'after_setup_theme', 'register_navwalker' );

function enqueue_custom_scripts() {
    // External Scripts:
    
    // GSAP core
    wp_enqueue_script( 'gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.0/gsap.min.js', array(), '3.11.0', true );
    // GSAP ScrollTrigger (depends on GSAP)
    wp_enqueue_script( 'scroll-trigger', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.0/ScrollTrigger.min.js', array('gsap'), '3.11.0', true );
    // Typed.js
    wp_enqueue_script( 'typed-js', 'https://cdn.jsdelivr.net/npm/typed.js@2.0.12', array(), '2.0.12', true );
  
    // Custom JS Files:
    wp_enqueue_script( 'hero-animation', get_template_directory_uri() . '/assets/js/heroAnimation.js', array('gsap', 'scroll-trigger'), '1.0', true );
    wp_enqueue_script( 'background-squares', get_template_directory_uri() . '/assets/js/backgroundSquares.js', array('gsap', 'scroll-trigger'), '1.0', true );
    wp_enqueue_script( 'portfolio-hover', get_template_directory_uri() . '/assets/js/portfolioHover.js', array('gsap', 'scroll-trigger'), '1.0', true );
    wp_enqueue_script( 'typed-init', get_template_directory_uri() . '/assets/js/typedInit.js', array('typed-js'), '1.0', true );
    wp_enqueue_script( 'persistent-cta', get_template_directory_uri() . '/assets/js/persistentCTA.js', array(), '1.0', true );
    wp_enqueue_script( 'logo-scroll', get_template_directory_uri() . '/assets/js/logoScroll.js', array(), '1.0', true );
    wp_enqueue_script( 'change-theme', get_template_directory_uri() . '/assets/js/changeTheme.js', array(), '1.0', true );
    wp_enqueue_script( 'helpers', get_template_directory_uri() . '/assets/js/helpers.js', array(), '1.0', true );
  }
  add_action( 'wp_enqueue_scripts', 'enqueue_custom_scripts' );
  
?>

