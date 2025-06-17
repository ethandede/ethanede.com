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
        
        // Add Projects link
        $items .= '<li class="menu-item"><a href="' . get_post_type_archive_link('project') . '">Projects</a></li>';
    }
    
    return $items;
}
add_filter('wp_nav_menu_items', 'ee_add_homepage_menu_items', 10, 2);

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
        wp_enqueue_script('typed-js', 'https://cdn.jsdelivr.net/npm/typed.js@2.0.12', [], '2.0.12', true);
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

    // Register Technology Taxonomy
    register_taxonomy('technology', ['project', 'deliverable'], [
        'labels' => [
            'name' => 'Technologies',
            'singular_name' => 'Technology',
            'menu_name' => 'Technologies',
            'all_items' => 'All Technologies',
            'edit_item' => 'Edit Technology',
            'view_item' => 'View Technology',
            'update_item' => 'Update Technology',
            'add_new_item' => 'Add New Technology',
            'new_item_name' => 'New Technology Name',
            'search_items' => 'Search Technologies',
            'popular_items' => 'Popular Technologies',
            'separate_items_with_commas' => 'Separate technologies with commas',
            'add_or_remove_items' => 'Add or remove technologies',
            'choose_from_most_used' => 'Choose from the most used technologies',
            'not_found' => 'No technologies found',
            'no_terms' => 'No technologies',
            'filter_by_item' => 'Filter by technology',
            'items_list_navigation' => 'Technologies list navigation',
            'items_list' => 'Technologies list',
            'back_to_items' => '← Go to technologies',
            'item_link' => 'Technology Link',
            'item_link_description' => 'A link to a technology'
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

        // Technology filter
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

    register_taxonomy('project_category', array('project'), $args);
}
add_action('init', 'register_project_category_taxonomy');

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

// Flush rewrite rules on theme activation
function ethanede_theme_activation() {
    register_project_post_type();
    register_project_category_taxonomy();
    register_project_tag_taxonomy();
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