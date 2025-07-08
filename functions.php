<?php

// Development mode toggle for easier cache busting
function is_development_mode() {
    // Set to true when actively developing to force cache busting
    return true; // Change to false when done developing
}

function disable_cache_headers() {
    // Only apply cache headers on front-end, non-REST API requests
    if (!is_admin() && !defined('REST_REQUEST')) {
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        
        // Add Safari-specific cache headers
        if (is_development_mode()) {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Pragma: no-cache");
            header("Expires: 0");
        }
    }
}
add_action('send_headers', 'disable_cache_headers');

// Add theme support and enqueue styles
function ee_enqueue_assets() {
    $manifest_path = get_template_directory() . '/assets/css/rev-manifest.json';
    $style_file = 'main.css';

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
        get_template_directory_uri() . '/assets/css/main.css',
        [],
        null,
        'all'
    );

    // Enqueue sidebar accordion JS
    wp_enqueue_script(
        'ee-sidebar-accordion',
        get_template_directory_uri() . '/assets/js/sidebar-accordion.js',
        [],
        is_development_mode() ? time() : null,
        true
    );

    // Enqueue Font Awesome Pro
    wp_enqueue_script(
        'font-awesome-pro',
        'https://kit.fontawesome.com/297056f51e.js',
        [],
        null,
        false
    );
    
    // Enqueue duotone Font Awesome kit
    wp_enqueue_script(
        'fontawesome-duotone',
        'https://kit.fontawesome.com/529c155b03.js',
        [],
        null,
        false
    );
}
add_action('wp_enqueue_scripts', 'ee_enqueue_assets');

// =============================================================================
// GOOGLE FONTS WITH FOUC PREVENTION
// =============================================================================

// Consolidated Google Fonts loading that works with your FOUC prevention
function ee_enqueue_google_fonts() {
    // Define font families - keep weights that match your SCSS
    $font_families = [
        'Roboto:wght@300,400,500,600,700',
        'Merriweather:ital,opsz,wght@0,18..144,300..900;1,18..144,300..900' // Simplified to match your actual usage
    ];
    
    $font_url = 'https://fonts.googleapis.com/css2?family=' . 
                implode('&family=', $font_families) . 
                '&display=swap';
    
    // Enqueue the font stylesheet
    wp_enqueue_style(
        'ee-google-fonts',
        $font_url,
        [],
        null
    );
}
add_action('wp_enqueue_scripts', 'ee_enqueue_google_fonts');

// Critical font preloading for FOUC prevention
function ee_preload_critical_fonts_for_hero() {
    // Always preconnect for faster loading
    echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    
    // Preload ONLY the fonts used in hero section to prevent FOUC
    if (is_front_page() || is_home()) {
        // Hero typically uses Merriweather for h1 and Roboto for body
        echo '<link rel="preload" as="font" type="font/woff2" href="https://fonts.gstatic.com/s/Merriweather/v30/u-4n0qyriQwlOrhSvowK_l52xwNZWMf6hPvhPQ.woff2" crossorigin>' . "\n";
        echo '<link rel="preload" as="font" type="font/woff2" href="https://fonts.gstatic.com/s/roboto/v30/KFOmCnqEu92Fr1Mu4mxKKTU1Kg.woff2" crossorigin>' . "\n";
    }
}
add_action('wp_head', 'ee_preload_critical_fonts_for_hero', 1);
function ee_font_loading_css() {
    echo '<style>
        /* Font loading optimization */
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        
        /* Hide main content until fonts are loaded to prevent FOUT, but keep contact form visible */
        .fonts-loading body > *:not(.contact-section) {
            visibility: hidden;
        }
        
        /* Always keep contact form visible */
        .fonts-loading .contact-section,
        .fonts-loading .contact-section * {
            visibility: visible !important;
        }
        
        /* Show text when fonts are loaded */
        .fonts-loaded body,
        .fonts-failed body {
            visibility: visible;
        }
        
        .fonts-loaded body > *,
        .fonts-failed body > * {
            visibility: visible;
        }
        
        /* Apply Google fonts after they load - match SCSS variables */
        .fonts-loaded body,
        .fonts-loaded p {
            font-family: "Roboto", system-ui, -apple-system, "Segoe UI", Helvetica, Arial, sans-serif;
        }
        
        .fonts-loaded h1, 
        .fonts-loaded h2, 
        .fonts-loaded h3, 
        .fonts-loaded h4 {
            font-family: "Merriweather", Georgia, "Times New Roman", serif;
        }
        
        /* h5 and h6 use Roboto in the SCSS */
        .fonts-loaded h5,
        .fonts-loaded h6 {
            font-family: "Roboto", system-ui, -apple-system, "Segoe UI", Helvetica, Arial, sans-serif;
        }
        
        /* Safari-specific font loading improvements */
        @supports (-webkit-appearance: none) {
            .fonts-loaded h1, 
            .fonts-loaded h2, 
            .fonts-loaded h3, 
            .fonts-loaded h4 {
                font-family: "Merriweather", "Georgia", "Times New Roman", serif !important;
                font-display: swap;
            }
            
            .fonts-loaded body,
            .fonts-loaded p {
                font-family: "Roboto", system-ui, -apple-system, "Segoe UI", Helvetica, Arial, sans-serif !important;
                font-display: swap;
            }
        }
        
        /* Fallback for users with slow connections or mobile */
        @media (prefers-reduced-motion: reduce) {
            .fonts-loading body > * {
                visibility: visible;
            }
        }
        
        /* Force show content on mobile after 3 seconds */
        @media (max-width: 768px) {
            .fonts-loading body > * {
                animation: forceShow 3s forwards;
            }
        }
        
        @keyframes forceShow {
            to {
                visibility: visible;
            }
        }
        
        /* Ensure navigation is always visible */
        .fonts-loading .site-nav,
        .fonts-loading .site-nav * {
            visibility: visible !important;
        }
    </style>' . "\n";
}
add_action('wp_head', 'ee_font_loading_css', 2);

// Add favicon support
function ee_add_favicon() {
    $favicon_svg = get_template_directory_uri() . '/assets/img/logo_ethanEde_blue.svg';
    $favicon_png = get_template_directory_uri() . '/assets/img/logo_ethanEde_blue.png';
    
    // SVG favicon for modern browsers (preferred)
    echo '<link rel="icon" type="image/svg+xml" href="' . esc_url($favicon_svg) . '">' . "\n";
    
    // PNG fallback for older browsers
    echo '<link rel="icon" type="image/png" href="' . esc_url($favicon_png) . '">' . "\n";
    
    // Apple touch icon for iOS devices
    echo '<link rel="apple-touch-icon" href="' . esc_url($favicon_png) . '">' . "\n";
    
    // Windows tile icon
    echo '<meta name="msapplication-TileImage" content="' . esc_url($favicon_png) . '">' . "\n";
    echo '<meta name="msapplication-TileColor" content="#45748C">' . "\n";
    
    // Additional meta tags for better favicon support
    echo '<meta name="theme-color" content="#45748C">' . "\n";
    echo '<meta name="msapplication-config" content="none">' . "\n";
    
    // Web app manifest for PWA support
    echo '<link rel="manifest" href="' . esc_url(get_template_directory_uri() . '/site.webmanifest') . '">' . "\n";
}
add_action('wp_head', 'ee_add_favicon', 1);

// Add font loading detection script
function ee_font_loading_script() {
    echo '<script>
        (function() {
            // Add loading class initially
            document.documentElement.classList.add("fonts-loading");
            
            // Function to mark fonts as loaded
            function markFontsLoaded() {
                document.documentElement.classList.remove("fonts-loading");
                document.documentElement.classList.add("fonts-loaded");
            }
            
            // Function to mark fonts as failed
            function markFontsFailed() {
                document.documentElement.classList.remove("fonts-loading");
                document.documentElement.classList.add("fonts-failed");
            }
            
            // Check if fonts are already loaded (cached)
            if (document.fonts && document.fonts.ready) {
                document.fonts.ready.then(function() {
                    markFontsLoaded();
                }).catch(function() {
                    markFontsFailed();
                });
            } else {
                // Fallback for browsers without Font Loading API
                setTimeout(function() {
                    markFontsLoaded();
                }, 1500); // Reduced timeout to 1.5 seconds
            }
            
            // Safari-specific font loading detection
            var isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
            var isMobile = /iPad|iPhone|iPod|Android/.test(navigator.userAgent);
            
            // Shorter timeout for Safari mobile
            var timeout = (isSafari && isMobile) ? 1500 : 2000;
            
            setTimeout(function() {
                if (document.documentElement.classList.contains("fonts-loading")) {
                    markFontsFailed();
                }
            }, timeout);
            
            // Force show content after 3 seconds as ultimate fallback
            setTimeout(function() {
                if (document.documentElement.classList.contains("fonts-loading")) {
                    markFontsFailed();
                }
            }, 3000);
        })();
    </script>' . "\n";
}
add_action('wp_head', 'ee_font_loading_script', 3);

// Register menus
function ee_register_menus() {
    register_nav_menus([
        'main_navigation' => __('Main Navigation', 'ethanede'),
    ]);
}
add_action('init', 'ee_register_menus');

// Add homepage-specific menu items
function ee_add_homepage_menu_items($items, $args) {
    if ($args->theme_location === 'main_navigation') {
        // Start with an empty menu
        $items = '';
        
        // Add homepage-specific items
        if (is_front_page()) {
            $items .= '<li class="menu-item"><a href="#about">About</a></li>';
            $items .= '<li class="menu-item"><a href="#skills">Skills</a></li>';
            $items .= '<li class="menu-item"><a href="#contact" class="contact-trigger">Contact</a></li>';
        } else {
            // Add Contact link to non-homepage menus
            $items .= '<li class="menu-item"><a href="' . home_url() . '#contact" class="contact-trigger">Contact</a></li>';
        }
        
        // Add Work link (unified projects and deliverables)
        $items .= '<li class="menu-item"><a href="' . home_url('/work/') . '">Work</a></li>';
    }
    
    return $items;
}
add_filter('wp_nav_menu_items', 'ee_add_homepage_menu_items', 10, 2);

function register_navwalker() {
    require_once get_template_directory() . '/includes/class-wp-bootstrap-navwalker.php';
}
add_action('after_setup_theme', 'register_navwalker');

// Enqueue main CSS with cache busting
function ee_enqueue_styles() {
    $css_version = filemtime(get_template_directory() . '/assets/css/main.css');
    
    // Add timestamp in development mode for easier cache busting
    if (is_development_mode()) {
        $css_version = $css_version . '.' . time();
    }
    
    wp_enqueue_style('main-style', get_template_directory_uri() . '/assets/css/main.css', array(), $css_version);
}
add_action('wp_enqueue_scripts', 'ee_enqueue_styles');

// Enqueue scripts with cache busting
function ee_enqueue_scripts() {
    // GSAP and ScrollTrigger
    wp_enqueue_script('gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js', array(), '3.12.2', true);
    wp_enqueue_script('scroll-trigger', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js', array('gsap'), '3.12.2', true);
    
    // Typed.js
    wp_enqueue_script('typed-js', 'https://cdn.jsdelivr.net/npm/typed.js@2.0.12', [], '2.0.12', true);
    
    // Background squares with cache busting
    $bg_squares_version = filemtime(get_template_directory() . '/assets/js/background-squares.js');
    wp_enqueue_script('background-squares', get_template_directory_uri() . '/assets/js/background-squares.js', ['gsap', 'scroll-trigger'], $bg_squares_version, true);
    
    // Other scripts with cache busting
    $scripts = [
        'helpers' => '/assets/js/helpers.js',
        'hero-animation' => '/assets/js/hero-animation.js',
        'logo-scroll' => '/assets/js/logo-scroll.js',
        'mobile-menu' => '/assets/js/mobile-menu.js',
        'persistent-cta' => '/assets/js/persistent-cta.js',
        'portfolio-hover' => '/assets/js/portfolio-hover.js',
        'typed-init' => '/assets/js/typed-init.js',
        'contact' => '/assets/js/contact.js',
        'gallery' => '/assets/js/gallery.js',
        'change-theme' => '/assets/js/change-theme.js'
    ];
    
    foreach ($scripts as $handle => $path) {
        $file_path = get_template_directory() . $path;
        if (file_exists($file_path)) {
            $version = filemtime($file_path);
            wp_enqueue_script($handle, get_template_directory_uri() . $path, array(), $version, true);
        }
    }
    
    // Load homepage-specific scripts
    if (is_front_page() || is_home()) {
        wp_enqueue_script('logo-scroll', get_template_directory_uri() . '/assets/js/logo-scroll.js', [], filemtime(get_template_directory() . '/assets/js/logo-scroll.js'), true);
    }
    
    // Load work-filter only on work archive page
    if (is_page('work') || is_post_type_archive(['project', 'deliverable'])) {
        wp_enqueue_script('work-filter', get_template_directory_uri() . '/assets/js/work-filter.js', [], filemtime(get_template_directory() . '/assets/js/work-filter.js'), true);
    }
    
    // Localize script for AJAX
    wp_localize_script('contact', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'ee_enqueue_scripts');

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
    
    // Register custom image sizes for better performance
    add_image_size('card-thumbnail', 400, 300, true); // 4:3 aspect ratio
    add_image_size('card-thumbnail-large', 600, 450, true); // For retina displays
    add_image_size('deliverable-hero', 1200, 600, true);
    add_image_size('project-hero', 1200, 600, true);

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

// Save ACF JSON files to the acf-json directory
add_filter('acf/settings/save_json', function ($path) {
    $path = get_stylesheet_directory() . '/acf-json';
    return $path;
});
// Load ACF JSON files from the acf-json directory
add_filter('acf/settings/load_json', function ($paths) {
    $paths[] = get_stylesheet_directory() . '/acf-json';
    return $paths;
});

// =============================================================================
// MASTER CARD SYSTEM HELPER FUNCTIONS
// =============================================================================

/**
 * Render a card using the master card template partial
 * 
 * @param array $args Card configuration array (see partials/card.php for full documentation)
 * @return void Outputs HTML directly
 */
function ee_render_card($args) {
    if (empty($args) || !is_array($args)) {
        return;
    }
    
    // Include the card partial
    get_template_part('partials/card', null, $args);
}

/**
 * Generate card arguments for a project
 * 
 * @param int $post_id Project post ID
 * @param string $context Card context ('archive', 'single', 'work', 'home')
 * @param array $overrides Optional array of values to override defaults
 * @return array Card arguments array
 */
function ee_get_project_card_args($post_id, $context = 'archive', $overrides = []) {
    $defaults = [
        'type' => 'project',
        'context' => $context,
        'post_id' => $post_id,
        'show_media_types' => false,
    ];
    
    // Context-specific defaults
    if ($context === 'work') {
        $defaults['show_media_types'] = true;
    }
    
    return wp_parse_args($overrides, $defaults);
}

/**
 * Generate card arguments for a deliverable
 * 
 * @param int $post_id Deliverable post ID
 * @param string $context Card context ('archive', 'single', 'work', 'home')
 * @param array $overrides Optional array of values to override defaults
 * @return array Card arguments array
 */
function ee_get_deliverable_card_args($post_id, $context = 'archive', $overrides = []) {
    $defaults = [
        'type' => 'deliverable',
        'context' => $context,
        'post_id' => $post_id,
        'show_media_types' => true,
    ];
    
    return wp_parse_args($overrides, $defaults);
}

/**
 * Render a project card
 * 
 * @param int $post_id Project post ID
 * @param string $context Card context ('archive', 'single', 'work', 'home')
 * @param array $overrides Optional array of values to override defaults
 * @return void Outputs HTML directly
 */
function ee_render_project_card($post_id, $context = 'archive', $overrides = []) {
    $args = ee_get_project_card_args($post_id, $context, $overrides);
    ee_render_card($args);
}

/**
 * Render a deliverable card
 * 
 * @param int $post_id Deliverable post ID
 * @param string $context Card context ('archive', 'single', 'work', 'home')
 * @param array $overrides Optional array of values to override defaults
 * @return void Outputs HTML directly
 */
function ee_render_deliverable_card($post_id, $context = 'archive', $overrides = []) {
    $args = ee_get_deliverable_card_args($post_id, $context, $overrides);
    ee_render_card($args);
}

// Auto-populate Project Title field with WordPress title
function ee_auto_populate_project_title($field) {
    if (!$field['value'] && isset($_GET['post']) && get_post_type($_GET['post']) === 'project') {
        $post_title = get_the_title($_GET['post']);
        if ($post_title) {
            $field['value'] = $post_title;
        }
    }
    return $field;
}
add_filter('acf/load_field/name=project_title', 'ee_auto_populate_project_title');

/**
 * Get How I Work projects
 * 
 * @param int $posts_per_page Number of posts to return
 * @return WP_Query
 */
function get_how_i_work_projects($posts_per_page = -1) {
    return new WP_Query([
        'post_type' => 'project',
        'posts_per_page' => $posts_per_page,
        'tax_query' => [
            [
                'taxonomy' => 'project_category',
                'field' => 'slug',
                'terms' => 'how-i-work',
            ],
        ],
        'orderby' => 'menu_order',
        'order' => 'ASC',
    ]);
}

// Register Deliverable Post Type and Taxonomies
function register_deliverable_post_type() {
    // Register Deliverable Post Type
    register_post_type('deliverable', [
        'labels' => [
            'name' => 'Deliverables',
            'singular_name' => 'Deliverable',
            'menu_name' => 'Deliverables',
            'all_items' => 'All Deliverables',
            'edit_item' => 'Edit Deliverable',
            'view_item' => 'View Deliverable',
            'add_new_item' => 'Add New Deliverable',
            'search_items' => 'Search Deliverables',
        ],
        'public' => true,
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'],
        'has_archive' => true,
        'menu_icon' => 'dashicons-portfolio',
        'rewrite' => ['slug' => 'deliverables'],
    ]);

    // Register Deliverable Type Taxonomy
    register_taxonomy('deliverable_type', ['deliverable'], [
        'labels' => [
            'name' => 'Deliverable Types',
            'singular_name' => 'Deliverable Type',
            'menu_name' => 'Deliverable Types',
        ],
        'hierarchical' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'deliverable-type'],
    ]);

    // Register Tool Taxonomy
    register_taxonomy('technology', ['project', 'deliverable'], [
        'labels' => [
            'name' => 'Tools',
            'singular_name' => 'Tool',
            'menu_name' => 'Tools',
            'all_items' => 'All Tools',
            'edit_item' => 'Edit Tool',
            'view_item' => 'View Tool',
            'update_item' => 'Update Tool',
            'add_new_item' => 'Add New Tool',
            'new_item_name' => 'New Tool Name',
            'search_items' => 'Search Tools',
            'popular_items' => 'Popular Tools',
            'separate_items_with_commas' => 'Separate tools with commas',
            'add_or_remove_items' => 'Add or remove tools',
            'choose_from_most_used' => 'Choose from the most used tools',
            'not_found' => 'No tools found',
            'no_terms' => 'No tools',
            'filter_by_item' => 'Filter by tool',
            'items_list_navigation' => 'Tools list navigation',
            'items_list' => 'Tools list',
            'back_to_items' => '← Go to tools',
            'item_link' => 'Tool Link',
            'item_link_description' => 'A link to a tool'
        ],
        'hierarchical' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'technology'],
    ]);

    // Register Skill Taxonomy
    register_taxonomy('skill', ['project', 'deliverable'], [
        'labels' => [
            'name' => 'Skills',
            'singular_name' => 'Skill',
            'menu_name' => 'Skills',
            'all_items' => 'All Skills',
            'edit_item' => 'Edit Skill',
            'view_item' => 'View Skill',
            'update_item' => 'Update Skill',
            'add_new_item' => 'Add New Skill',
            'new_item_name' => 'New Skill Name',
            'search_items' => 'Search Skills',
            'popular_items' => 'Popular Skills',
            'separate_items_with_commas' => 'Separate skills with commas',
            'add_or_remove_items' => 'Add or remove skills',
            'choose_from_most_used' => 'Choose from the most used skills',
            'not_found' => 'No skills found',
            'no_terms' => 'No skills',
            'filter_by_item' => 'Filter by skill',
            'items_list_navigation' => 'Skills list navigation',
            'items_list' => 'Skills list',
            'back_to_items' => '← Go to skills',
            'item_link' => 'Skill Link',
            'item_link_description' => 'A link to a skill'
        ],
        'hierarchical' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'skill'],
    ]);
}
add_action('init', 'register_deliverable_post_type');

// Modify deliverable archive query based on filters
function modify_deliverable_archive_query($query) {
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('deliverable')) {
        $tax_query = [];
        
        // Project filter
        if (isset($_GET['project']) && !empty($_GET['project'])) {
            $query->set('meta_query', [
                [
                    'key' => 'related_project',
                    'value' => $_GET['project'],
                    'compare' => '='
                ]
            ]);
        }

        // Type filter
        if (isset($_GET['type']) && !empty($_GET['type'])) {
            $tax_query[] = [
                'taxonomy' => 'deliverable_type',
                'field' => 'slug',
                'terms' => $_GET['type']
            ];
        }

        // Tool filter
        if (isset($_GET['technology']) && !empty($_GET['technology'])) {
            $tax_query[] = [
                'taxonomy' => 'technology',
                'field' => 'slug',
                'terms' => $_GET['technology']
            ];
        }

        // Skill filter
        if (isset($_GET['skill']) && !empty($_GET['skill'])) {
            $tax_query[] = [
                'taxonomy' => 'skill',
                'field' => 'slug',
                'terms' => $_GET['skill']
            ];
        }

        // Add tax query if we have any
        if (!empty($tax_query)) {
            $query->set('tax_query', $tax_query);
        }

        // Set posts per page
        $query->set('posts_per_page', 12);
    }
}
add_action('pre_get_posts', 'modify_deliverable_archive_query');

// Add custom query vars for filtering
function add_deliverable_query_vars($vars) {
    $vars[] = 'project';
    $vars[] = 'type';
    $vars[] = 'technology';
    $vars[] = 'skill';
    return $vars;
}
add_filter('query_vars', 'add_deliverable_query_vars');

// Register Company Taxonomy
function register_company_taxonomy() {
    register_taxonomy('company', ['project', 'deliverable'], [
        'labels' => [
            'name' => 'Companies',
            'singular_name' => 'Company',
            'menu_name' => 'Companies',
            'all_items' => 'All Companies',
            'edit_item' => 'Edit Company',
            'view_item' => 'View Company',
            'update_item' => 'Update Company',
            'add_new_item' => 'Add New Company',
            'new_item_name' => 'New Company Name',
            'search_items' => 'Search Companies',
            'popular_items' => 'Popular Companies',
            'separate_items_with_commas' => 'Separate companies with commas',
            'add_or_remove_items' => 'Add or remove companies',
            'choose_from_most_used' => 'Choose from the most used companies',
            'not_found' => 'No companies found',
            'no_terms' => 'No companies',
            'filter_by_item' => 'Filter by company',
            'items_list_navigation' => 'Companies list navigation',
            'items_list' => 'Companies list',
            'back_to_items' => '← Go to companies',
            'item_link' => 'Company Link',
            'item_link_description' => 'A link to a company'
        ],
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => ['slug' => 'company'],
        'capabilities' => [
            'manage_terms' => 'manage_categories',
            'edit_terms' => 'manage_categories',
            'delete_terms' => 'manage_categories',
            'assign_terms' => 'edit_posts'
        ],
    ]);
}
add_action('init', 'register_company_taxonomy');

// Include taxonomy migration script
require_once get_template_directory() . '/migrate-taxonomies.php';

/**
 * Register Project Category Taxonomy
 */
function register_project_category_taxonomy() {
    $labels = array(
        'name'              => _x('Project Categories', 'taxonomy general name', 'ethanede'),
        'singular_name'     => _x('Project Category', 'taxonomy singular name', 'ethanede'),
        'search_items'      => __('Search Project Categories', 'ethanede'),
        'all_items'         => __('All Project Categories', 'ethanede'),
        'parent_item'       => __('Parent Project Category', 'ethanede'),
        'parent_item_colon' => __('Parent Project Category:', 'ethanede'),
        'edit_item'         => __('Edit Project Category', 'ethanede'),
        'update_item'       => __('Update Project Category', 'ethanede'),
        'add_new_item'      => __('Add New Project Category', 'ethanede'),
        'new_item_name'     => __('New Project Category Name', 'ethanede'),
        'menu_name'         => __('Categories', 'ethanede'),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'project-category'),
        'show_in_rest'      => true,
    );

    register_taxonomy('project_category', array('project', 'post'), $args);
}
add_action('init', 'register_project_category_taxonomy');

// Disable default categories and tags for posts and projects
function disable_default_taxonomies() {
    // Remove category support from posts
    remove_post_type_support('post', 'category');
    
    // Remove tag support from both posts and projects
    remove_post_type_support('post', 'post_tag');
    remove_post_type_support('project', 'post_tag');
    
    // Remove category and tag metaboxes from edit screens
    add_action('admin_menu', function() {
        // Remove from posts
        remove_meta_box('categorydiv', 'post', 'side');
        remove_meta_box('tagsdiv-post_tag', 'post', 'side');
        
        // Remove from projects
        remove_meta_box('categorydiv', 'project', 'side');
        remove_meta_box('tagsdiv-post_tag', 'project', 'side');
        remove_meta_box('tagsdiv-project_tag', 'project', 'side');
        remove_meta_box('companydiv', 'project', 'side');
        remove_meta_box('tagsdiv-company', 'project', 'side');
        remove_meta_box('postimagediv', 'project', 'side');
        remove_meta_box('postimagediv', 'project', 'normal');
        remove_meta_box('postimagediv', 'project', 'advanced');
    });
    
    // Completely unregister the default taxonomies
    add_action('init', function() {
        unregister_taxonomy_for_object_type('category', 'post');
        unregister_taxonomy_for_object_type('post_tag', 'post');
        unregister_taxonomy_for_object_type('post_tag', 'project');
    }, 99);
}
add_action('init', 'disable_default_taxonomies');

// Additional featured image removal for projects
function remove_project_featured_image_metabox() {
    remove_meta_box('postimagediv', 'project', 'side');
    remove_meta_box('postimagediv', 'project', 'normal');
    remove_meta_box('postimagediv', 'project', 'advanced');
}
add_action('add_meta_boxes', 'remove_project_featured_image_metabox');

// Remove default categories and tags from admin menu
function remove_default_taxonomy_admin_menus() {
    // Remove categories
    remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=category');
    
    // Remove tags
    remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=post_tag');
    
    // Also try removing with the parent slug
    global $submenu;
    if (isset($submenu['edit.php'])) {
        foreach ($submenu['edit.php'] as $key => $menu_item) {
            if (strpos($menu_item[2], 'edit-tags.php?taxonomy=category') !== false ||
                strpos($menu_item[2], 'edit-tags.php?taxonomy=post_tag') !== false) {
                unset($submenu['edit.php'][$key]);
            }
        }
    }
    
    // Remove from Projects admin menu too
    if (isset($submenu['edit.php?post_type=project'])) {
        foreach ($submenu['edit.php?post_type=project'] as $key => $menu_item) {
            if (strpos($menu_item[2], 'edit-tags.php?taxonomy=post_tag') !== false ||
                strpos($menu_item[2], 'edit-tags.php?taxonomy=category') !== false) {
                unset($submenu['edit.php?post_type=project'][$key]);
            }
        }
    }
}
add_action('admin_menu', 'remove_default_taxonomy_admin_menus', 999);

// Additional cleanup for taxonomy references
function remove_taxonomy_admin_references() {
    // Remove from admin bar
    add_action('admin_bar_menu', function($wp_admin_bar) {
        $wp_admin_bar->remove_node('new-category');
        $wp_admin_bar->remove_node('new-post_tag');
    }, 999);
}
add_action('init', 'remove_taxonomy_admin_references');

/**
 * Register Project Tag Taxonomy
 */
function register_project_tag_taxonomy() {
    $labels = array(
        'name'              => _x('Project Tags', 'taxonomy general name', 'ethanede'),
        'singular_name'     => _x('Project Tag', 'taxonomy singular name', 'ethanede'),
        'search_items'      => __('Search Project Tags', 'ethanede'),
        'all_items'         => __('All Project Tags', 'ethanede'),
        'parent_item'       => __('Parent Project Tag', 'ethanede'),
        'parent_item_colon' => __('Parent Project Tag:', 'ethanede'),
        'edit_item'         => __('Edit Project Tag', 'ethanede'),
        'update_item'       => __('Update Project Tag', 'ethanede'),
        'add_new_item'      => __('Add New Project Tag', 'ethanede'),
        'new_item_name'     => __('New Project Tag Name', 'ethanede'),
        'menu_name'         => __('Tags', 'ethanede'),
    );

    $args = array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'project-tag'),
        'show_in_rest'      => true,
    );

    register_taxonomy('project_tag', array('project'), $args);
}
add_action('init', 'register_project_tag_taxonomy');

// Register Project Post Type
function register_project_post_type() {
    $labels = array(
        'name'               => _x('Projects', 'post type general name', 'ethanede'),
        'singular_name'      => _x('Project', 'post type singular name', 'ethanede'),
        'menu_name'          => _x('Projects', 'admin menu', 'ethanede'),
        'name_admin_bar'     => _x('Project', 'add new on admin bar', 'ethanede'),
        'add_new'            => _x('Add New', 'project', 'ethanede'),
        'add_new_item'       => __('Add New Project', 'ethanede'),
        'new_item'           => __('New Project', 'ethanede'),
        'edit_item'          => __('Edit Project', 'ethanede'),
        'view_item'          => __('View Project', 'ethanede'),
        'all_items'          => __('All Projects', 'ethanede'),
        'search_items'       => __('Search Projects', 'ethanede'),
        'parent_item_colon'  => __('Parent Projects:', 'ethanede'),
        'not_found'          => __('No projects found.', 'ethanede'),
        'not_found_in_trash' => __('No projects found in Trash.', 'ethanede')
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'projects'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'thumbnail', 'custom-fields'),
        'show_in_rest'       => true,
        'menu_icon'          => 'dashicons-portfolio'
    );

    register_post_type('project', $args);
}
add_action('init', 'register_project_post_type');

// Modify project archive query
function modify_project_archive_query($query) {
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('project')) {
        $query->set('post_type', 'project');
        $query->set('posts_per_page', 12);
        $query->set('orderby', 'date');
        $query->set('order', 'DESC');
    }
}
add_action('pre_get_posts', 'modify_project_archive_query');

// Create a page-based work archive instead of custom rewrite rules
function create_work_page_on_activation() {
    // Check if work page already exists
    $work_page = get_page_by_path('work');
    
    if (!$work_page) {
        // Create the work page
        $page_data = array(
            'post_title'    => 'My work',
            'post_content'  => '',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_name'     => 'work',
            'meta_input' => array(
                '_wp_page_template' => 'page-work.php'
            )
        );
        
        $page_id = wp_insert_post($page_data);
        
        if ($page_id) {
            error_log('Work page created with ID: ' . $page_id);
        }
    }
}
add_action('after_switch_theme', 'create_work_page_on_activation');

// Manually create work page if it doesn't exist (run this once)
function maybe_create_work_page() {
    create_work_page_on_activation();
}
// Uncomment the line below to create the page, then comment it back out
// add_action('init', 'maybe_create_work_page');

// Redirect old archive pages to unified work page
function redirect_to_unified_work_page() {
    if (is_post_type_archive('project') || is_post_type_archive('deliverable')) {
        wp_redirect(home_url('/work/'), 301);
        exit;
    }
}
add_action('template_redirect', 'redirect_to_unified_work_page');

// Flush rewrite rules on theme activation
function ethanede_theme_activation() {
    register_project_post_type();
    register_project_category_taxonomy();
    register_project_tag_taxonomy();
    disable_default_taxonomies();
    create_work_page_on_activation();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'ethanede_theme_activation');

function enqueue_gallery_assets() {
    if (is_singular('deliverable')) {
        wp_enqueue_style('gallery-styles', get_template_directory_uri() . '/assets/css/gallery.css', array(), '1.0.0');
        wp_enqueue_script('gallery-scripts', get_template_directory_uri() . '/assets/js/gallery.js', array(), '1.0.0', true);
    }
}
add_action('wp_enqueue_scripts', 'enqueue_gallery_assets');

// Enqueue ACF admin scripts and styles
function enqueue_acf_admin_scripts() {
    // Only load on ACF edit screens for deliverables
    $screen = get_current_screen();
    if ($screen && ($screen->post_type === 'deliverable' || $screen->id === 'deliverable')) {
        wp_enqueue_script(
            'acf-video-selector',
            get_template_directory_uri() . '/assets/js/acf-video-selector.js',
            ['jquery', 'acf-input'],
            '1.0',
            true
        );
        
        wp_enqueue_style(
            'acf-admin-styles',
            get_template_directory_uri() . '/assets/css/acf-admin.css',
            [],
            '1.0'
        );
    }
}
add_action('admin_enqueue_scripts', 'enqueue_acf_admin_scripts');

// AJAX handler to get attachment data for video selector
function get_attachment_data_ajax() {
    // Verify nonce for security
    if (!wp_verify_nonce($_POST['nonce'], 'acf_nonce')) {
        wp_die('Security check failed');
    }
    
    $attachment_ids = isset($_POST['attachment_ids']) ? $_POST['attachment_ids'] : [];
    if (!is_array($attachment_ids)) {
        wp_send_json_error('Invalid attachment IDs');
        return;
    }
    
    $attachments_data = [];
    
    foreach ($attachment_ids as $attachment_id) {
        $attachment_id = intval($attachment_id);
        if ($attachment_id <= 0) continue;
        
        // Get attachment post
        $attachment = get_post($attachment_id);
        if (!$attachment || $attachment->post_type !== 'attachment') continue;
        
        // Get attachment metadata
        $metadata = wp_get_attachment_metadata($attachment_id);
        $mime_type = get_post_mime_type($attachment_id);
        
        // Build attachment data similar to ACF format
        $attachment_data = [
            'id' => $attachment_id,
            'title' => $attachment->post_title,
            'filename' => basename(get_attached_file($attachment_id)),
            'url' => wp_get_attachment_url($attachment_id),
            'alt' => get_post_meta($attachment_id, '_wp_attachment_image_alt', true),
            'mime_type' => $mime_type,
            'type' => strpos($mime_type, 'video/') === 0 ? 'video' : 'image'
        ];
        
        $attachments_data[] = $attachment_data;
    }
    
    wp_send_json_success($attachments_data);
}
add_action('wp_ajax_get_attachment_data', 'get_attachment_data_ajax');

// Populate video selection choices dynamically
function populate_video_selection_choices($field) {
    // Only apply to our specific field
    if ($field['name'] !== 'video_selection') {
        return $field;
    }
    
    // Get the current post ID
    global $post;
    if (!$post || $post->post_type !== 'deliverable') {
        return $field;
    }
    
    // Get the deliverable media
    $media = get_field('deliverable_media', $post->ID);
    if (!$media || !is_array($media)) {
        return $field;
    }
    
    $choices = [];
    
    foreach ($media as $item) {
        // Handle both attachment ID format and full object format
        if (is_numeric($item)) {
            $attachment_id = intval($item);
            $mime_type = get_post_mime_type($attachment_id);
            
            if (strpos($mime_type, 'video/') === 0) {
                $attachment = get_post($attachment_id);
                $filename = basename(get_attached_file($attachment_id));
                $title = $attachment ? $attachment->post_title : $filename;
                
                $display_text = $title;
                if ($title !== $filename) {
                    $display_text .= ' (' . $filename . ')';
                }
                
                $choices[$filename] = $display_text;
            }
        } elseif (is_array($item) && isset($item['type']) && $item['type'] === 'video') {
            $filename = $item['filename'] ?? $item['name'] ?? 'video.mp4';
            $title = $item['title'] ?? $item['alt'] ?? $filename;
            
            $display_text = $title;
            if ($title !== $filename) {
                $display_text .= ' (' . $filename . ')';
            }
            
            $choices[$filename] = $display_text;
        }
    }
    
    $field['choices'] = $choices;
    
    return $field;
}
add_filter('acf/load_field/name=video_selection', 'populate_video_selection_choices');

// Validate video selection field to ensure it's a valid choice
function validate_video_selection($valid, $value, $field, $input) {
    if (!$valid || empty($value)) {
        return $valid;
    }
    
    // Get the current post ID
    global $post;
    if (!$post || $post->post_type !== 'deliverable') {
        return $valid;
    }
    
    // Get the deliverable media to validate against
    $media = get_field('deliverable_media', $post->ID);
    if (!$media || !is_array($media)) {
        return 'No videos found in media gallery';
    }
    
    $valid_filenames = [];
    
    foreach ($media as $item) {
        if (is_numeric($item)) {
            $attachment_id = intval($item);
            $mime_type = get_post_mime_type($attachment_id);
            
            if (strpos($mime_type, 'video/') === 0) {
                $filename = basename(get_attached_file($attachment_id));
                $valid_filenames[] = $filename;
            }
        } elseif (is_array($item) && isset($item['type']) && $item['type'] === 'video') {
            $filename = $item['filename'] ?? $item['name'] ?? 'video.mp4';
            $valid_filenames[] = $filename;
        }
    }
    
    if (!in_array($value, $valid_filenames)) {
        return 'Selected video is not valid. Please refresh and select a video from the dropdown.';
    }
    
    return $valid;
}
add_filter('acf/validate_value/name=video_selection', 'validate_video_selection', 10, 4);

// Allow GIF uploads in Media Library
function allow_gif_uploads($mimes) {
    // Explicitly add GIF support
    $mimes['gif'] = 'image/gif';
    
    // Remove any restrictions on GIF files
    if (isset($mimes['gif'])) {
        unset($mimes['gif']);
    }
    
    // Add GIF back with full support
    $mimes['gif'] = 'image/gif';
    
    return $mimes;
}
add_filter('upload_mimes', 'allow_gif_uploads', 10, 1);

// Ensure GIF files are not filtered out
function ensure_gif_upload($file) {
    if ($file['type'] === 'image/gif') {
        $file['error'] = 0;
    }
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'ensure_gif_upload');

// Allow SVG uploads for ACF fields (complement to Safe SVG plugin)
function allow_svg_for_acf($mimes) {
    // Ensure SVG is allowed even if Safe SVG plugin is not active
    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    
    return $mimes;
}
add_filter('upload_mimes', 'allow_svg_for_acf', 10, 1);

// Ensure SVG files are properly handled in ACF image fields
function ensure_svg_upload_for_acf($file) {
    if ($file['type'] === 'image/svg+xml' || $file['type'] === 'image/svg') {
        $file['error'] = 0;
    }
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'ensure_svg_upload_for_acf');

// Additional SVG support for ACF specifically
function acf_svg_support() {
    // Allow SVG in ACF image fields
    add_filter('acf/upload_prefilter/type=image', function($errors, $file, $field) {
        if ($file['type'] === 'image/svg+xml' || pathinfo($file['name'], PATHINFO_EXTENSION) === 'svg') {
            return $errors; // Allow the upload
        }
        return $errors;
    }, 10, 3);
}
add_action('acf/init', 'acf_svg_support');

// Add custom columns to project_category taxonomy
add_filter('manage_edit-project_category_columns', 'add_project_category_taxonomy_columns');

// Clean deliverable content from unwanted CSS classes
function clean_deliverable_content($content) {
    if (!$content) return '';
    
    // Remove auto-generated CSS classes (CSS-in-JS, React Native Web, etc.)
    $patterns = [
        // Remove CSS-in-JS classes like css-175oi2r, css-146c3p1
        '/\s*class="[^"]*css-[^"]*"/',
        // Remove React Native Web classes like r-bcqeeo, r-1ttztb7
        '/\s*class="[^"]*r-[^"]*"/',
        // Remove any class attribute that contains only auto-generated classes
        '/\s*class="[^"]*(?:css-[a-z0-9]+\s*|r-[a-z0-9]+\s*)+"/i',
        // Remove empty class attributes
        '/\s*class=""\s*/',
        // Remove dir attributes that are typically auto-added
        '/\s*dir="[^"]*"/',
    ];
    
    $clean_content = $content;
    foreach ($patterns as $pattern) {
        $clean_content = preg_replace($pattern, '', $clean_content);
    }
    
    // Clean up any double spaces or empty attributes
    $clean_content = preg_replace('/\s+/', ' ', $clean_content);
    $clean_content = preg_replace('/<(\w+)\s+>/', '<$1>', $clean_content);
    
    // Convert divs to paragraphs where appropriate
    $clean_content = str_replace(['<div>', '</div>'], ['<p>', '</p>'], $clean_content);
    
    // Remove empty paragraphs and divs
    $clean_content = preg_replace('/<p[^>]*>\s*<\/p>/', '', $clean_content);
    $clean_content = preg_replace('/<div[^>]*>\s*<\/div>/', '', $clean_content);
    
    // Remove nested spans that are redundant
    $clean_content = preg_replace('/<span[^>]*><span[^>]*>(.*?)<\/span><\/span>/', '$1', $clean_content);
    $clean_content = preg_replace('/<span[^>]*>(.*?)<\/span>/', '$1', $clean_content);
    
    return $clean_content;
}
function add_project_category_taxonomy_columns($columns) {
    unset($columns['posts']);
    $columns['image'] = __('Image', 'ethanede');
    $columns['display_order'] = __('Display Order', 'ethanede');
    $columns['posts'] = __('Projects', 'ethanede');
    return $columns;
}

add_action('manage_project_category_custom_column', 'manage_project_category_taxonomy_columns', 10, 3);
function manage_project_category_taxonomy_columns($content, $column_name, $term_id) {
    switch ($column_name) {
        case 'image':
            $image = get_field('category_image', 'project_category_' . $term_id);
            if ($image) {
                $content = '<img src="' . esc_url($image) . '" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;" />';
            } else {
                $content = '—';
            }
            break;
        case 'display_order':
            $order = get_field('field_display_order', 'project_category_' . $term_id);
            $content = $order ? $order : '—';
            break;
    }
    return $content;
}

// Make order column sortable
add_filter('manage_edit-project_category_sortable_columns', 'make_project_category_columns_sortable');
function make_project_category_columns_sortable($columns) {
    $columns['order'] = 'order';
    return $columns;
}

// Add order field to Quick Edit
add_action('quick_edit_custom_box', 'project_category_quick_edit_fields', 10, 3);
function project_category_quick_edit_fields($column_name, $screen, $taxonomy) {
    if ($taxonomy !== 'project_category' || $column_name !== 'order') {
        return;
    }
    ?>
    <fieldset>
        <div class="inline-edit-col">
            <label>
                <span class="title">Display Order</span>
                <span class="input-text-wrap">
                    <input type="number" name="display_order" class="ptitle" value="" min="0" step="1">
                </span>
            </label>
        </div>
    </fieldset>
    <?php
}

// Save Quick Edit data
add_action('edited_project_category', 'save_project_category_quick_edit');
function save_project_category_quick_edit($term_id) {
    if (isset($_POST['display_order'])) {
        update_field('field_display_order', $_POST['display_order'], 'project_category_' . $term_id);
    }
}

// Add JavaScript to populate Quick Edit field with current value
add_action('admin_footer', 'project_category_quick_edit_javascript');
function project_category_quick_edit_javascript() {
    global $current_screen;
    if ($current_screen->id !== 'edit-project_category') {
        return;
    }
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Populate quick edit field with current order value
        $('.editinline').on('click', function() {
            var $row = $(this).closest('tr');
            var order = $row.find('.column-order').text().trim();
            
            // Handle empty/dash values
            if (order === '—' || order === '') {
                order = '';
            }
            
            setTimeout(function() {
                $('input[name="display_order"]').val(order);
            }, 100);
        });
    });
    </script>
    <?php
}

/**
 * Email Configuration and Contact Form 7 Customizations
 */

// Ensure WordPress can send emails
function ee_configure_wp_mail() {
    // You can add SMTP configuration here if needed
    // For now, we'll just ensure the from email is properly set
    add_filter('wp_mail_from', 'ee_custom_wp_mail_from');
    add_filter('wp_mail_from_name', 'ee_custom_wp_mail_from_name');
    
    // For local development, try to fix common mail issues
    if (strpos(home_url(), '.local') !== false) {
        add_action('phpmailer_init', 'ee_configure_local_mail');
    }
}
add_action('init', 'ee_configure_wp_mail');

function ee_custom_wp_mail_from($original_email_address) {
    // For local development, use your Gmail address
    if (strpos(home_url(), '.local') !== false) {
        return 'ethanede@gmail.com';
    }
    
    // Get the site domain for production
    $sitename = wp_parse_url(network_home_url(), PHP_URL_HOST);
    if (substr($sitename, 0, 4) === 'www.') {
        $sitename = substr($sitename, 4);
    }
    return 'noreply@' . $sitename;
}

function ee_custom_wp_mail_from_name($original_email_from) {
    return 'Ethan Ede Website';
}

// Configure PHPMailer for local development
function ee_configure_local_mail($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host = 'localhost';
    $phpmailer->Port = 1025; // Local by Flywheel's MailHog port
    $phpmailer->SMTPAuth = false;
    $phpmailer->SMTPSecure = false;
    $phpmailer->Username = '';
    $phpmailer->Password = '';
    
    // Disable SSL verification for local development
    $phpmailer->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
}

// Add custom validation and processing for Contact Form 7
add_action('wpcf7_before_send_mail', 'ee_customize_cf7_mail');
function ee_customize_cf7_mail($contact_form) {
    // Get form ID to ensure we're only affecting our contact form
    if ($contact_form->id() == 15) { // Updated to use the actual form ID from your HTML
        // Add any custom processing here
        // For example, you could save submissions to database, 
        // integrate with CRM, add additional notifications, etc.
        
        // Log the submission for debugging (only if WP_DEBUG is enabled)
        if (WP_DEBUG) {
            error_log('Contact form submission received from form ID: ' . $contact_form->id());
        }
    }
}

// Customize Contact Form 7 validation messages
add_filter('wpcf7_messages', 'ee_customize_cf7_messages');
function ee_customize_cf7_messages($messages) {
    $messages['mail_sent_ok'] = "Thank you for your message. I'll get back to you soon!";
    $messages['mail_sent_ng'] = "There was an error sending your message. Please try again.";
    $messages['validation_error'] = "Please check the highlighted fields below.";
    $messages['spam'] = "Your message was flagged as spam. Please try again.";
    $messages['accept_terms'] = "You must accept the terms and conditions.";
    $messages['invalid_required'] = "This field is required.";
    $messages['invalid_too_long'] = "This field is too long.";
    $messages['invalid_too_short'] = "This field is too short.";
    
    return $messages;
}

// Remove Contact Form 7 auto-paragraph formatting for better control
add_filter('wpcf7_autop_or_not', '__return_false');

// Custom Contact Form 7 styling and scripting
add_action('wpcf7_enqueue_scripts', 'ee_cf7_custom_scripts');
function ee_cf7_custom_scripts() {
    // Minimal styling that works with existing design
    // Only add background colors for better UX, preserving existing layout
    wp_add_inline_style('contact-form-7', '
        .wpcf7-response-output.wpcf7-mail-sent-ok {
            background: rgba(76, 175, 80, 0.1);
            border-left: 3px solid #4CAF50;
        }
        
        .wpcf7-response-output.wpcf7-validation-errors,
        .wpcf7-response-output.wpcf7-mail-sent-ng {
            background: rgba(244, 67, 54, 0.1);
            border-left: 3px solid #f44336;
        }
        
        .wpcf7-response-output.wpcf7-spam {
            background: rgba(255, 152, 0, 0.1);
            border-left: 3px solid #ff9800;
        }
    ');
}

// Noindex unused taxonomy archives
function noindex_unused_taxonomy_archives() {
    if (is_tax(['technology', 'skill', 'company'])) {
        echo '<meta name="robots" content="noindex, nofollow">' . "\n";
    }
}
add_action('wp_head', 'noindex_unused_taxonomy_archives');

/**
 * Helper function to convert plural term names to singular for display
 * 
 * @param string $term_name The original term name
 * @return string The display-friendly term name
 */
function get_singular_term_display_name($term_name) {
    $conversions = [
        'Microsites' => 'Microsite',
        'Animations' => 'Animation',
        // Add more conversions here as needed
    ];
    
    return isset($conversions[$term_name]) ? $conversions[$term_name] : $term_name;
}

/**
 * Display a single tag above h1 on template pages
 * Follows the same logic as work cards: "Project" for projects, deliverable type for deliverables
 * 
 * @param int $post_id Optional post ID, defaults to current post
 * @param array $args Optional configuration arguments
 * @return void Outputs HTML directly
 */
function ee_display_single_page_tag($post_id = null, $args = []) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    if (!$post_id) {
        return;
    }
    
    $post_type = get_post_type($post_id);
    $tag_text = '';
    $tag_classes = ['single-page-tag'];
    
    // Default configuration
    $defaults = [
        'wrapper_class' => 'single-page-tag-wrapper',
        'show_wrapper' => true,
        'additional_classes' => []
    ];
    $config = wp_parse_args($args, $defaults);
    
    // Determine tag text and classes based on post type
    if ($post_type === 'project') {
        $tag_text = 'Project';
        $tag_classes[] = 'tag-project';
    } elseif ($post_type === 'deliverable') {
        $type_terms = get_the_terms($post_id, 'deliverable_type');
        if ($type_terms && !is_wp_error($type_terms)) {
            $type_term = $type_terms[0];
            $tag_text = get_singular_term_display_name($type_term->name);
            $tag_classes[] = 'tag-deliverable';
        }
    } elseif ($post_type === 'post') {
        // For blog posts, show "Service" tag with icon since these are high-level practice areas
        $tag_text = '<i class="fa-duotone fa-briefcase"></i>Service';
        $tag_classes[] = 'tag-service-area';
        $tag_classes[] = 'tag-with-icon';
    }
    
    // Add any additional classes
    if (!empty($config['additional_classes'])) {
        $tag_classes = array_merge($tag_classes, (array)$config['additional_classes']);
    }
    
    // Only display if we have tag text
    if ($tag_text) {
        if ($config['show_wrapper']) {
            echo '<div class="' . esc_attr($config['wrapper_class']) . '">';
        }
        
        echo '<span class="' . esc_attr(implode(' ', $tag_classes)) . '">';
        // Allow HTML for icons in tag text, but escape if no HTML detected
        if (strpos($tag_text, '<') !== false) {
            echo wp_kses($tag_text, [
                'i' => [
                    'class' => [],
                    'aria-hidden' => []
                ]
            ]);
        } else {
            echo esc_html($tag_text);
        }
        echo '</span>';
        
        if ($config['show_wrapper']) {
            echo '</div>';
        }
    }
}

// Add mobile-specific font loading optimizations
function ee_mobile_font_optimizations() {
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">' . "\n";
    
    // Remove aggressive cache control that might interfere with font loading
    // echo '<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">' . "\n";
    // echo '<meta http-equiv="Pragma" content="no-cache">' . "\n";
    // echo '<meta http-equiv="Expires" content="0">' . "\n";
}
add_action('wp_head', 'ee_mobile_font_optimizations', 1);

// Safari-specific cache busting script
function ee_safari_cache_bust() {
    if (is_development_mode()) {
        echo '<script>
            // Safari cache busting for development
            if (navigator.userAgent.includes("Safari") && !navigator.userAgent.includes("Chrome")) {
                // Force reload on Safari if page was cached
                if (performance.navigation.type === 1) {
                    // Page was reloaded, clear any cached resources
                    if ("caches" in window) {
                        caches.keys().then(function(names) {
                            for (let name of names) {
                                caches.delete(name);
                            }
                        });
                    }
                }
                
                // Add cache busting parameter to CSS/JS requests
                const links = document.querySelectorAll("link[rel=\'stylesheet\']");
                links.forEach(function(link) {
                    if (link.href && !link.href.includes("?")) {
                        link.href = link.href + "?v=" + Date.now();
                    }
                });
            }
        </script>' . "\n";
    }
}
add_action('wp_footer', 'ee_safari_cache_bust', 999);

// =============================================================================
// CUSTOM ACF FIELD: EXCEL/GOOGLE SHEETS EMBED
// =============================================================================

// Register custom ACF field type for Excel embeds
function register_excel_embed_acf_field() {
    if (function_exists('acf_register_field_type')) {
        // Include the custom field class
        include_once get_template_directory() . '/includes/class-acf-field-excel-embed.php';
        acf_register_field_type('ACF_Field_Excel_Embed');
    }
}
add_action('acf/include_field_types', 'register_excel_embed_acf_field');

// Enqueue scripts and styles for Excel embed field
function enqueue_excel_embed_assets() {
    if (is_admin() || has_acf_field('excel_embed')) {
        wp_enqueue_script(
            'excel-embed-admin',
            get_template_directory_uri() . '/assets/js/excel-embed-admin.js',
            ['jquery'],
            is_development_mode() ? time() : '1.0.0',
            true
        );
        
        // Localize admin script with AJAX data
        wp_localize_script('excel-embed-admin', 'excelEmbedAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('excel_embed_nonce')
        ]);
        
        wp_enqueue_style(
            'excel-embed-admin',
            get_template_directory_uri() . '/assets/css/excel-embed-admin.css',
            [],
            is_development_mode() ? time() : '1.0.0'
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_excel_embed_assets');
add_action('admin_enqueue_scripts', 'enqueue_excel_embed_assets');

// Frontend Excel embed styles
function enqueue_excel_embed_frontend() {
    // Load on single deliverable pages or if Excel embed field is present
    if (is_singular('deliverable') || has_acf_field('excel_embed')) {
        wp_enqueue_style(
            'excel-embed-frontend',
            get_template_directory_uri() . '/assets/css/excel-embed-frontend.css',
            [],
            is_development_mode() ? time() : '1.0.0'
        );
        
        wp_enqueue_script(
            'excel-embed-frontend',
            get_template_directory_uri() . '/assets/js/excel-embed-frontend.js',
            ['jquery'],
            is_development_mode() ? time() : '1.0.0',
            true
        );
        
        // Localize script with AJAX URL and nonce
        wp_localize_script('excel-embed-frontend', 'excelEmbedAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('excel_embed_nonce')
        ]);
    }
}
add_action('wp_enqueue_scripts', 'enqueue_excel_embed_frontend');

// AJAX handler for fetching Google Sheets data
function fetch_google_sheets_data() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'excel_embed_nonce')) {
        wp_die('Security check failed');
    }
    
    $sheet_url = sanitize_url($_POST['sheet_url']);
    $sheet_name = sanitize_text_field($_POST['sheet_name']);
    
    // Extract sheet ID from Google Sheets URL
    $pattern = '/\/spreadsheets\/d\/([a-zA-Z0-9-_]+)/';
    if (!preg_match($pattern, $sheet_url, $matches)) {
        wp_send_json_error('Invalid Google Sheets URL');
    }
    
    $sheet_id = $matches[1];
    
    // Try API method first, then fallback to CSV export
    $api_key = get_option('google_sheets_api_key', '');
    
    if (!empty($api_key)) {
        // Try API method
        $result = fetch_via_api($sheet_id, $sheet_name, $api_key);
        if ($result['success']) {
            wp_send_json_success($result['data']);
        }
        // If API fails, continue to fallback method
        error_log('API method failed: ' . $result['error']);
    } else {
        // No API key configured, try CSV export
        $result = fetch_via_csv_export($sheet_id, $sheet_name);
        if ($result['success']) {
            wp_send_json_success($result['data']);
        } else {
            wp_send_json_error($result['error']);
        }
        return;
    }
    
    // Fallback: Use CSV export (no API key required)
    $result = fetch_via_csv_export($sheet_id, $sheet_name);
    if ($result['success']) {
        wp_send_json_success($result['data']);
    } else {
        wp_send_json_error($result['error']);
    }
}

// Fetch data via Google Sheets API
function fetch_via_api($sheet_id, $sheet_name, $api_key) {
    // If no sheet name provided, try to get sheet metadata to find the correct sheet
    if (empty($sheet_name)) {
        // Get sheet metadata to find the correct sheet name
        $metadata_url = "https://sheets.googleapis.com/v4/spreadsheets/{$sheet_id}?key=" . $api_key;
        $metadata_response = wp_remote_get($metadata_url);
        
        if (!is_wp_error($metadata_response)) {
            $metadata_body = wp_remote_retrieve_body($metadata_response);
            $metadata = json_decode($metadata_body, true);
            
            if ($metadata && isset($metadata['sheets'])) {
                // Use the first sheet name
                $sheet_name = $metadata['sheets'][0]['properties']['title'];
            }
        }
    }
    
    $range = $sheet_name ? $sheet_name . '!A:Z' : 'A:Z';
    $api_url = "https://sheets.googleapis.com/v4/spreadsheets/{$sheet_id}/values/{$range}?key=" . $api_key;
    
    $response = wp_remote_get($api_url);
    
    if (is_wp_error($response)) {
        return ['success' => false, 'error' => 'Failed to fetch data from Google Sheets: ' . $response->get_error_message()];
    }
    
    $response_code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if ($response_code !== 200) {
        return ['success' => false, 'error' => "API Error (Code {$response_code}): " . $body];
    }
    
    if (!$data) {
        return ['success' => false, 'error' => 'Invalid JSON response from Google Sheets API'];
    }
    
    if (isset($data['error'])) {
        return ['success' => false, 'error' => 'Google Sheets API Error: ' . $data['error']['message']];
    }
    
    if (!isset($data['values'])) {
        return ['success' => false, 'error' => 'No data found in sheet via API'];
    }
    
    // Process the data to handle frozen panes and custom headers
    $processed_data = process_sheet_data($data['values']);
    
    return ['success' => true, 'data' => $processed_data];
}

// Fetch data via CSV export (fallback method)
function fetch_via_csv_export($sheet_id, $sheet_name) {
    // Extract gid from the original URL if available
    $gid = 0; // Default to first sheet
    
    // Check if we have the original sheet URL to extract gid
    if (isset($_POST['sheet_url'])) {
        $original_url = $_POST['sheet_url'];
        if (preg_match('/gid=(\d+)/', $original_url, $matches)) {
            $gid = $matches[1];
        }
    }
    
    // For sheets with frozen panes, we need to use a different approach
    // Try to get the full range including frozen columns
    $csv_url = "https://docs.google.com/spreadsheets/d/{$sheet_id}/export?format=csv&gid={$gid}&range=A:Z";
    
    $response = wp_remote_get($csv_url);
    
    if (is_wp_error($response)) {
        return ['success' => false, 'error' => 'Failed to fetch CSV data: ' . $response->get_error_message()];
    }
    
    $response_code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    
    if ($response_code !== 200) {
        return ['success' => false, 'error' => "CSV Export Error (Code {$response_code}): " . $body];
    }
    
    if (empty($body)) {
        return ['success' => false, 'error' => 'No data found in CSV export'];
    }
    
    // Parse CSV data
    $rows = array_map('str_getcsv', explode("\n", $body));
    
    // Remove empty rows
    $rows = array_filter($rows, function($row) {
        return !empty(array_filter($row, function($cell) {
            return !empty(trim($cell));
        }));
    });
    
    if (empty($rows)) {
        return ['success' => false, 'error' => 'No data found in CSV export'];
    }
    
    // Process rows to handle frozen panes and custom headers
    $processed_rows = process_sheet_data($rows);
    
    return ['success' => true, 'data' => $processed_rows];
}
add_action('wp_ajax_fetch_google_sheets_data', 'fetch_google_sheets_data');
add_action('wp_ajax_nopriv_fetch_google_sheets_data', 'fetch_google_sheets_data');

// Process sheet data to handle frozen panes and custom headers
function process_sheet_data($rows) {
    if (empty($rows)) {
        return [];
    }
    
    // Look for the actual header row (usually row 5 in frozen pane sheets)
    $header_row_index = 0;
    $data_start_index = 1;
    
    // Check if this looks like a frozen pane sheet (first few rows might be empty or have frozen content)
    $first_few_rows = array_slice($rows, 0, 6);
    
    // Look for a row that has meaningful headers (not empty, not just numbers)
    foreach ($first_few_rows as $index => $row) {
        if (count($row) > 3) { // At least 4 columns
            $non_empty_cells = array_filter($row, function($cell) {
                return !empty(trim($cell)) && !is_numeric(trim($cell));
            });
            
            // If this row has several non-numeric, non-empty cells, it's likely the header
            if (count($non_empty_cells) >= 3) {
                $header_row_index = $index;
                $data_start_index = $index + 1;
                break;
            }
        }
    }
    
    // Extract headers from the identified header row
    $headers = $rows[$header_row_index];
    
    // Clean up headers (remove empty ones and trim)
    $headers = array_map(function($header) {
        return trim($header);
    }, $headers);
    
    // Remove completely empty columns
    $valid_columns = [];
    foreach ($headers as $index => $header) {
        if (!empty($header)) {
            $valid_columns[] = $index;
        }
    }
    
    // Filter headers to only include non-empty columns
    $clean_headers = array_values(array_filter($headers));
    
    // Process data rows (skip the header row and any empty rows before it)
    $data_rows = [];
    for ($i = $data_start_index; $i < count($rows); $i++) {
        $row = $rows[$i];
        
        // Only include rows that have data in the valid columns
        $has_data = false;
        foreach ($valid_columns as $col_index) {
            if (isset($row[$col_index]) && !empty(trim($row[$col_index]))) {
                $has_data = true;
                break;
            }
        }
        
        if ($has_data) {
            // Extract only the data from valid columns
            $data_row = [];
            foreach ($valid_columns as $col_index) {
                $data_row[] = isset($row[$col_index]) ? trim($row[$col_index]) : '';
            }
            $data_rows[] = $data_row;
        }
    }
    
    // Return processed data with clean headers
    return array_merge([$clean_headers], $data_rows);
}

// Helper function to check if page has Excel embed field
function has_acf_field($field_name) {
    if (!function_exists('get_field_objects')) {
        return false;
    }
    
    global $post;
    if (!$post) {
        return false;
    }
    
    $fields = get_field_objects($post->ID);
    if (!$fields) {
        return false;
    }
    
    foreach ($fields as $field) {
        if ($field['name'] === $field_name || $field['type'] === $field_name) {
            return true;
        }
    }
    
    return false;
}

// Template function to render Excel embed
function render_excel_embed($field_value, $args = []) {
    if (empty($field_value) || !is_array($field_value)) {
        return '';
    }
    
    $defaults = [
        'title' => 'Spreadsheet Data',
        'enable_search' => true,
        'enable_filters' => true,
        'show_status_indicators' => true,
        'rows_per_page' => 5,
        'custom_styling' => true
    ];
    
    $args = wp_parse_args($args, $defaults);
    
    // Merge field settings with args
    $settings = array_merge($args, $field_value);
    
    $container_class = 'excel-embed-container';
    if ($settings['custom_styling']) {
        $container_class .= ' excel-custom-styling';
    }
    
    $data_attrs = [
        'sheet-url' => esc_attr($settings['sheet_url']),
        'sheet-name' => esc_attr($settings['sheet_name']),
        'enable-search' => $settings['enable_search'] ? 'true' : 'false',
        'enable-filters' => $settings['enable_filters'] ? 'true' : 'false',
        'show-status-indicators' => $settings['show_status_indicators'] ? 'true' : 'false',
        'rows-per-page' => intval($settings['rows_per_page'])
    ];
    
    $data_attr_string = '';
    foreach ($data_attrs as $key => $value) {
        $data_attr_string .= ' data-' . $key . '="' . $value . '"';
    }
    
    ob_start();
    ?>
    <div class="<?php echo esc_attr($container_class); ?>"<?php echo $data_attr_string; ?>>
        <div class="excel-embed-header">
            <h3 class="excel-embed-title"><?php echo esc_html($settings['title']); ?></h3>
            
            <?php if ($settings['enable_search'] || $settings['enable_filters']): ?>
                <div class="excel-embed-controls">
                    <?php if ($settings['enable_search']): ?>
                        <div class="excel-search-box">
                            <input type="text" placeholder="Search data..." />
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($settings['enable_filters']): ?>
                        <div class="excel-filter-dropdown">
                            <select>
                                <option value="">All Status</option>
                                <option value="done">Done</option>
                                <option value="pending">Pending</option>
                                <option value="verified">Verified</option>
                                <option value="in progress">In Progress</option>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="excel-loading">Loading spreadsheet data...</div>
    </div>
    <?php
    return ob_get_clean();
}

// Shortcode for Excel embeds
function excel_embed_shortcode($atts) {
    $atts = shortcode_atts([
        'field_name' => '',
        'post_id' => get_the_ID(),
        'title' => 'Spreadsheet Data',
        'enable_search' => 'true',
        'enable_filters' => 'true',
        'show_status_indicators' => 'true',
        'rows_per_page' => '20'
    ], $atts);
    
    if (empty($atts['field_name'])) {
        return '<p class="excel-error">Field name is required.</p>';
    }
    
    $field_value = get_field($atts['field_name'], $atts['post_id']);
    
    if (empty($field_value)) {
        return '<p class="excel-error">No Excel embed field found.</p>';
    }
    
    $args = [
        'title' => $atts['title'],
        'enable_search' => $atts['enable_search'] === 'true',
        'enable_filters' => $atts['enable_filters'] === 'true',
        'show_status_indicators' => $atts['show_status_indicators'] === 'true',
        'rows_per_page' => intval($atts['rows_per_page'])
    ];
    
    return render_excel_embed($field_value, $args);
}
add_shortcode('excel_embed', 'excel_embed_shortcode');

// Add admin menu for Google Sheets API configuration
function add_google_sheets_admin_menu() {
    add_options_page(
        'Google Sheets API',
        'Google Sheets API',
        'manage_options',
        'google-sheets-api',
        'render_google_sheets_admin_page'
    );
}
add_action('admin_menu', 'add_google_sheets_admin_menu');

// Render the admin page
function render_google_sheets_admin_page() {
    if (isset($_POST['submit'])) {
        if (wp_verify_nonce($_POST['google_sheets_nonce'], 'google_sheets_settings')) {
            $api_key = sanitize_text_field($_POST['google_sheets_api_key']);
            update_option('google_sheets_api_key', $api_key);
            echo '<div class="notice notice-success"><p>Google Sheets API key updated successfully!</p></div>';
        }
    }
    
    $current_api_key = get_option('google_sheets_api_key', '');
    ?>
    <div class="wrap">
        <h1>Google Sheets API Configuration</h1>
        <p>Configure your Google Sheets API key to enable Excel/Spreadsheet embeds on your website.</p>
        
        <form method="post" action="">
            <?php wp_nonce_field('google_sheets_settings', 'google_sheets_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="google_sheets_api_key">API Key</label>
                    </th>
                    <td>
                        <input type="text" 
                               id="google_sheets_api_key" 
                               name="google_sheets_api_key" 
                               value="<?php echo esc_attr($current_api_key); ?>" 
                               class="regular-text" />
                        <p class="description">
                            Enter your Google Sheets API key. You can get one from the 
                            <a href="https://console.developers.google.com/" target="_blank">Google Cloud Console</a>.
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Save API Key'); ?>
        </form>
        
        <div class="card">
            <h2>Setup Instructions</h2>
            <ol>
                <li>Go to the <a href="https://console.developers.google.com/" target="_blank">Google Cloud Console</a></li>
                <li>Create a new project or select an existing one</li>
                <li>Enable the Google Sheets API</li>
                <li>Create credentials (API Key)</li>
                <li>Copy the API key and paste it above</li>
                <li>Make sure your Google Sheets are set to "Anyone with the link can view"</li>
            </ol>
        </div>
    </div>
    <?php
}

/**
 * Custom Login Page Styles and Logo
 */
function ee_custom_login_logo() {
    $logo_url = get_template_directory_uri() . '/assets/img/logo_ethanEde_blue.svg';
    ?>
    <style type="text/css">
      :root {
        --surface-dark: #181a1b;
        --primary-color: #45748C;
        --text-primary: #f3f3f3;
        --text-secondary: #b3b3b3;
      }
      body.login {
        background: var(--surface-dark) !important;
        font-family: 'Roboto', system-ui, -apple-system, 'Segoe UI', Helvetica, Arial, sans-serif !important;
        color: var(--text-primary) !important;
      }
      .login h1 a {
        background-image: url('<?php echo esc_url($logo_url); ?>') !important;
        background-size: contain !important;
        background-position: center !important;
        background-repeat: no-repeat !important;
        width: 180px !important;
        height: 80px !important;
        margin-bottom: 10px !important;
        padding-bottom: 0 !important;
        box-shadow: none !important;
        outline: none !important;
      }
      .login form {
        background: rgba(30,34,36,0.98) !important;
        border-radius: 8px !important;
        box-shadow: 0 2px 16px rgba(0,0,0,0.18) !important;
        border: 1px solid rgba(255,255,255,0.08) !important;
      }
      .login label {
        color: var(--text-primary) !important;
        font-weight: 500 !important;
      }
      .login #login_error, .login .message, .login .success {
        border-left: 4px solid var(--primary-color) !important;
        color: var(--text-primary) !important;
        background: rgba(69,116,140,0.08) !important;
      }
      .login .button-primary {
        background: var(--primary-color) !important;
        border: none !important;
        color: var(--text-primary) !important;
        text-shadow: none !important;
        box-shadow: none !important;
        font-weight: 600 !important;
        border-radius: 4px !important;
        transition: background 0.2s !important;
      }
      .login .button-primary:hover, .login .button-primary:focus {
        background: #2d4a5c !important;
      }
      .login #backtoblog a, .login #nav a {
        color: var(--primary-color) !important;
      }
      .login #backtoblog a:hover, .login #nav a:hover {
        color: #2d4a5c !important;
      }
      .login form .input, .login input[type="text"], .login input[type="password"], .login input[type="email"] {
        background: rgba(255,255,255,0.08) !important;
        color: var(--text-primary) !important;
        border: 1px solid rgba(255,255,255,0.15) !important;
      }
      .login form .input:focus {
        border-color: var(--primary-color) !important;
        box-shadow: 0 0 0 1px var(--primary-color) !important;
      }
      .login .privacy-policy-page-link {
        color: var(--text-secondary) !important;
      }
    </style>
    <?php
}
add_action('login_enqueue_scripts', 'ee_custom_login_logo');

// Change login logo URL to site home
function ee_login_logo_url() {
    return home_url();
}
add_filter('login_headerurl', 'ee_login_logo_url');

// Change login logo title to site name
function ee_login_logo_url_title() {
    return get_bloginfo('name');
}
add_filter('login_headertext', 'ee_login_logo_url_title');