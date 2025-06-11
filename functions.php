<?php

function disable_cache_headers() {
    // Only apply cache headers on front-end, non-REST API requests
    if (!is_admin() && !defined('REST_REQUEST')) {
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }
}
add_action('send_headers', 'disable_cache_headers');

// Add theme support and enqueue styles
function ee_enqueue_assets() {
    $manifest_path = get_template_directory() . '/assets/css/rev-manifest.json';
    $style_file = 'style.css';

    // Safely handle manifest file
    if (file_exists($manifest_path)) {
        $manifest_content = file_get_contents($manifest_path);
        if ($manifest_content !== false) {
            $manifest = json_decode($manifest_content, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($manifest[$style_file])) {
                $style_file = $manifest[$style_file];
            }
        }
    }

    wp_enqueue_style(
        'ee-style',
        get_template_directory_uri() . '/assets/css/' . $style_file,
        [],
        null,
        'all'
    );

    // Enqueue Font Awesome
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css',
        [],
        '6.4.2'
    );

    // Enqueue Google Fonts
    wp_enqueue_style(
        'google-fonts',
        'https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&family=Roboto:wght@400;700&family=Merriweather:ital,opsz,wght@0,18..144,300..900;1,18..144,300..900&display=swap',
        [],
        null
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

function register_navwalker() {
    require_once get_template_directory() . '/includes/class-wp-bootstrap-navwalker.php';
}
add_action('after_setup_theme', 'register_navwalker');

// Enqueue custom scripts
function enqueue_custom_scripts() {
    // Only load scripts on front end
    if (!is_admin()) {
        wp_enqueue_script('gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.0/gsap.min.js', [], '3.11.0', true);
        wp_enqueue_script('scroll-trigger', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.0/ScrollTrigger.min.js', ['gsap'], '3.11.0', true);
        
        wp_enqueue_script('hero-animation', get_template_directory_uri() . '/assets/js/heroAnimation.js', ['gsap', 'scroll-trigger'], '1.0', true);
        wp_enqueue_script('portfolio-hover', get_template_directory_uri() . '/assets/js/portfolioHover.js', ['gsap', 'scroll-trigger'], '1.0', true);
        wp_enqueue_script('typed-init', get_template_directory_uri() . '/assets/js/typedInit.js', ['typed-js'], '1.0', true);
        wp_enqueue_script('persistent-cta', get_template_directory_uri() . '/assets/js/persistentCTA.js', ['gsap', 'scroll-trigger'], '1.0', true);
        wp_enqueue_script('change-theme', get_template_directory_uri() . '/assets/js/changeTheme.js', [], '1.0', true);
        wp_enqueue_script('helpers', get_template_directory_uri() . '/assets/js/helpers.js', [], '1.0', true);
        wp_enqueue_script('mobile-menu', get_template_directory_uri() . '/assets/js/mobileMenu.js', ['gsap'], '1.0', true);
        wp_enqueue_script('contact', get_template_directory_uri() . '/assets/js/contact.js', ['gsap'], '1.3', true);
        
        // Load background squares on homepage and single posts
        if (is_front_page() || is_home() || is_single()) {
            wp_enqueue_script('background-squares', get_template_directory_uri() . '/assets/js/backgroundSquares.js', ['gsap', 'scroll-trigger'], '1.0', true);
        }
        
        // Load homepage-specific scripts
        if (is_front_page() || is_home()) {
            wp_enqueue_script('typed-js', 'https://cdn.jsdelivr.net/npm/typed.js@2.0.12', [], '2.0.12', true);
            wp_enqueue_script('logo-scroll', get_template_directory_uri() . '/assets/js/logoScroll.js', [], '1.0', true);
        }
    }
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');

// Disable comments
function disable_comments() {
    // Disable support for comments and trackbacks in post types
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}
add_action('admin_init', 'disable_comments');

// Close comments on the front-end
function disable_comments_status() {
    return false;
}
add_filter('comments_open', 'disable_comments_status', 20, 2);
add_filter('pings_open', 'disable_comments_status', 20, 2);

// Hide existing comments
function hide_existing_comments($comments) {
    $comments = array();
    return $comments;
}
add_filter('comments_array', 'hide_existing_comments', 10, 2);

// Remove comments page from admin menu
function remove_comments_admin_menu() {
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'remove_comments_admin_menu');

function ethanede_setup() {
    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title.
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails on posts and pages.
    add_theme_support('post-thumbnails');

    // Register navigation menus
    register_nav_menus(array(
        'primary' => esc_html__('Primary Menu', 'ethanede'),
        'footer' => esc_html__('Footer Menu', 'ethanede'),
    ));

    // Switch default core markup to output valid HTML5.
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // Add theme support for selective refresh for widgets.
    add_theme_support('customize-selective-refresh-widgets');

    // Add support for editor styles.
    add_theme_support('editor-styles');

    // Add support for responsive embeds.
    add_theme_support('responsive-embeds');
}
add_action('after_setup_theme', 'ethanede_setup');