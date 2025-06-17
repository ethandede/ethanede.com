<?php
/**
 * Migration Script for Taxonomy Restructuring
 * 
 * This script helps migrate existing tags to the new taxonomy structure:
 * - Companies
 * - Deliverable Types
 * - Technologies
 * - Skills
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Add admin menu page
add_action('admin_menu', 'add_taxonomy_migration_page');
function add_taxonomy_migration_page() {
    add_management_page(
        'Taxonomy Migration',
        'Taxonomy Migration',
        'manage_options',
        'taxonomy-migration',
        'render_migration_page'
    );
}

// Define the mapping of existing tags to new taxonomies
$taxonomy_mapping = [
    'company' => [
        'Act-On',
        'Best Buy',
        'Bosch',
        'D-Link',
        'DHX Advertising',
        'Experian',
        'Kong',
        'Lightspeed Systems',
        'Liveops',
        'Los Angeles Clippers',
        'NBA',
        'Portland Japanese Garden',
        'Quiksilver',
        'Rehau',
        'Staples',
        'Toshiba'
    ],
    'deliverable_type' => [
        'Animations',
        'Banner Ads',
        'Microsites',
        'Branding',
        'Landing Pages',
        'Video Production',
        'Content Migration',
        'Reporting',
        'Music Production',
        'Sound Design'
    ],
    'technology' => [
        'Software' => [
            'Adobe Creative Cloud',
            'After Effects',
            'Figma',
            'Sketch',
            'Visual Studio Code',
            'Pro Tools',
            'Logic Pro',
            'Ableton Live'
        ],
        'Platforms' => [
            'WordPress',
            'Salesforce',
            'Pardot',
            'Drift',
            'Vidyard',
            'VWO'
        ],
        'Project Management' => [
            'Jira',
            'Monday',
            'Trello'
        ],
        'Analytics' => [
            'Google Analytics',
            'Google Tag Manager',
            'Looker Studio',
            'Piwik Pro'
        ],
        'Development' => [
            'GitHub',
            'Advanced Custom Fields'
        ],
        'AI Tools' => [
            'ChatGPT',
            'Grok',
            'Midjourney',
            'Cursor'
        ]
    ],
    'skill' => [
        'Technical Skills' => [
            'HTML',
            'CSS',
            'JavaScript',
            'PHP',
            'Sass'
        ],
        'Professional Skills' => [
            'Project Management',
            'SEO',
            'CRO',
            'Data Analysis',
            'Hosting'
        ],
        'Creative Skills' => [
            'Video Production',
            'Animation',
            'Music Production',
            'Sound Design',
            'Audio Engineering'
        ],
        'Design Skills' => [
            'UX Design',
            'UI Design',
            'Graphic Design',
            'Iconography',
            'Visual Design',
            'Brand Design',
            'Motion Graphics',
            'Typography',
            'Color Theory',
            'Layout Design',
            'Wireframing',
            'Prototyping'
        ]
    ]
];

// Define term descriptions
$term_descriptions = [
    'deliverable_type' => [
        'Animations' => 'Dynamic visual content including motion graphics, animated sequences, and interactive animations for web and digital platforms.',
        'Banner Ads' => 'Digital advertising banners and display ads optimized for various platforms and screen sizes.',
        'Microsites' => 'Focused, standalone websites designed for specific campaigns, products, or initiatives.',
        'Branding' => 'Comprehensive brand identity development including logos, style guides, and visual language systems.',
        'Landing Pages' => 'Conversion-optimized web pages designed for specific marketing campaigns or user journeys.',
        'Video Production' => 'End-to-end video content creation including filming, editing, and post-production.',
        'Content Migration' => 'Strategic transfer and optimization of content between platforms or systems.',
        'Reporting' => 'Data analysis and visualization for performance metrics and business insights.',
        'Music Production' => 'Original music composition, sound design, and audio production for various media.',
        'Sound Design' => 'Creation and manipulation of audio elements for enhanced user experience and storytelling.'
    ],
    'technology' => [
        'Software' => 'Industry-standard software tools and applications for digital content creation and development.',
        'Adobe Creative Cloud' => 'Industry-standard creative software suite for design, video, and digital content creation.',
        'After Effects' => 'Professional motion graphics and visual effects software for creating dynamic animations.',
        'Figma' => 'Collaborative interface design tool for creating and prototyping digital experiences.',
        'Sketch' => 'Vector-based design tool for creating user interfaces and digital graphics.',
        'Visual Studio Code' => 'Modern code editor with powerful features for web development.',
        'Pro Tools' => 'Professional digital audio workstation for music and sound production.',
        'Logic Pro' => 'Advanced music production and audio editing software.',
        'Ableton Live' => 'Creative music production and performance software.',
        'Platforms' => 'Digital platforms and systems for content management and business operations.',
        'WordPress' => 'Flexible content management system for building and managing websites.',
        'Salesforce' => 'Customer relationship management platform for business operations.',
        'Pardot' => 'Marketing automation platform for B2B marketing and sales alignment.',
        'Drift' => 'Conversational marketing platform for real-time customer engagement.',
        'Vidyard' => 'Video platform for business marketing and sales.',
        'VWO' => 'Conversion rate optimization and A/B testing platform.',
        'Project Management' => 'Tools and platforms for project organization and team collaboration.',
        'Jira' => 'Agile project management tool for software development teams.',
        'Monday' => 'Visual project management platform for team collaboration.',
        'Trello' => 'Flexible project organization tool using boards and cards.',
        'Analytics' => 'Tools for data analysis and business intelligence.',
        'Google Analytics' => 'Web analytics service for tracking and reporting website traffic.',
        'Google Tag Manager' => 'Tag management system for deploying marketing and analytics tags.',
        'Looker Studio' => 'Data visualization and business intelligence platform.',
        'Piwik Pro' => 'Privacy-focused analytics platform for data-driven insights.',
        'Development' => 'Tools and platforms for software development and version control.',
        'GitHub' => 'Platform for version control and collaborative software development.',
        'Advanced Custom Fields' => 'WordPress plugin for customizing content management.',
        'AI Tools' => 'Artificial intelligence tools for content creation and development assistance.',
        'ChatGPT' => 'Advanced language model for natural language processing and content generation.',
        'Grok' => 'AI assistant for data analysis and insights generation.',
        'Midjourney' => 'AI-powered image generation tool for creating unique visual content.',
        'Cursor' => 'AI-enhanced code editor for intelligent development assistance.'
    ],
    'skill' => [
        'Technical Skills' => 'Core technical abilities in web development and programming.',
        'HTML' => 'Markup language for structuring web content.',
        'CSS' => 'Styling language for web design and layout.',
        'JavaScript' => 'Programming language for interactive web development.',
        'PHP' => 'Server-side scripting language for web development.',
        'Sass' => 'CSS preprocessor for enhanced styling capabilities.',
        'Professional Skills' => 'Business and management capabilities for project success.',
        'Project Management' => 'Strategic planning and execution of digital projects.',
        'SEO' => 'Search engine optimization for improved online visibility.',
        'CRO' => 'Conversion rate optimization for better user engagement.',
        'Data Analysis' => 'Interpretation of data to drive business decisions.',
        'Hosting' => 'Management of web server infrastructure and deployment.',
        'Creative Skills' => 'Artistic and creative capabilities for content production.',
        'Video Production' => 'End-to-end video content creation and editing.',
        'Animation' => 'Creation of motion graphics and animated content.',
        'Music Production' => 'Composition and production of original music.',
        'Sound Design' => 'Creation and manipulation of audio elements.',
        'Audio Engineering' => 'Technical aspects of sound recording and production.',
        'Design Skills' => 'Visual and user experience design capabilities.',
        'UX Design' => 'User experience design focusing on user needs and interactions.',
        'UI Design' => 'User interface design for digital products and applications.',
        'Graphic Design' => 'Visual communication through typography and imagery.',
        'Iconography' => 'Design of symbolic visual elements and icons.',
        'Visual Design' => 'Creation of cohesive visual systems and aesthetics.',
        'Brand Design' => 'Development of brand identity and visual language.',
        'Motion Graphics' => 'Animation and visual effects for digital media.',
        'Typography' => 'Art and technique of arranging type for readability.',
        'Color Theory' => 'Strategic use of color in design and branding.',
        'Layout Design' => 'Arrangement of visual elements in space.',
        'Wireframing' => 'Creation of basic visual guides for interfaces.',
        'Prototyping' => 'Development of interactive models for testing.'
    ]
];

// Define company information
$company_info = [
    'Act-On' => [
        'description' => 'Marketing automation platform helping businesses create meaningful connections with their customers.',
        'industry' => 'Marketing Technology',
        'website' => 'https://www.act-on.com'
    ],
    'Best Buy' => [
        'description' => 'Multinational consumer electronics retailer and technology services company.',
        'industry' => 'Retail',
        'website' => 'https://www.bestbuy.com'
    ],
    'Bosch' => [
        'description' => 'Global supplier of technology and services, operating in four business sectors: Mobility, Industrial Technology, Consumer Goods, and Energy and Building Technology.',
        'industry' => 'Manufacturing & Technology',
        'website' => 'https://www.bosch.com'
    ],
    'D-Link' => [
        'description' => 'Global leader in networking and connectivity solutions for consumers and businesses.',
        'industry' => 'Networking & Technology',
        'website' => 'https://www.dlink.com'
    ],
    'DHX Advertising' => [
        'description' => 'Full-service advertising agency specializing in creative solutions for brands.',
        'industry' => 'Advertising',
        'website' => 'https://www.dhxadvertising.com'
    ],
    'Experian' => [
        'description' => 'Global information services company providing data and analytical tools to clients around the world.',
        'industry' => 'Information Services',
        'website' => 'https://www.experian.com'
    ],
    'Kong' => [
        'description' => 'Cloud connectivity company that enables developers to build, secure, and manage APIs and microservices.',
        'industry' => 'Cloud Computing',
        'website' => 'https://www.konghq.com'
    ],
    'Lightspeed Systems' => [
        'description' => 'Leading provider of cloud-based filtering and analytics solutions for K-12 schools.',
        'industry' => 'Education Technology',
        'website' => 'https://www.lightspeedsystems.com'
    ],
    'Liveops' => [
        'description' => 'Cloud contact center platform providing on-demand call center services and virtual call center agents.',
        'industry' => 'Customer Service',
        'website' => 'https://www.liveops.com'
    ],
    'Los Angeles Clippers' => [
        'description' => 'Professional basketball team competing in the National Basketball Association (NBA).',
        'industry' => 'Sports & Entertainment',
        'website' => 'https://www.nba.com/clippers'
    ],
    'NBA' => [
        'description' => 'National Basketball Association, the premier professional basketball league in North America.',
        'industry' => 'Sports & Entertainment',
        'website' => 'https://www.nba.com'
    ],
    'Portland Japanese Garden' => [
        'description' => 'Cultural institution featuring traditional Japanese gardens and cultural programming.',
        'industry' => 'Cultural Arts',
        'website' => 'https://japanesegarden.org'
    ],
    'Quiksilver' => [
        'description' => 'Global lifestyle brand for board sports, including surfing, snowboarding, and skateboarding.',
        'industry' => 'Apparel & Sports',
        'website' => 'https://www.quiksilver.com'
    ],
    'Rehau' => [
        'description' => 'Global polymer processing company providing solutions for construction, automotive, and industry.',
        'industry' => 'Manufacturing',
        'website' => 'https://www.rehau.com'
    ],
    'Staples' => [
        'description' => 'American office retail company providing office supplies and services.',
        'industry' => 'Retail',
        'website' => 'https://www.staples.com'
    ],
    'Toshiba' => [
        'description' => 'Multinational conglomerate manufacturing electronics and electrical equipment.',
        'industry' => 'Electronics',
        'website' => 'https://www.toshiba.com'
    ]
];

// Render the migration page
function render_migration_page() {
    global $taxonomy_mapping;
    
    // Handle form submission
    if (isset($_POST['run_migration']) && check_admin_referer('taxonomy_migration_nonce')) {
        migrate_taxonomies();
    }
    
    ?>
    <div class="wrap">
        <h1>Taxonomy Migration</h1>
        <p>This tool will help migrate your existing tags to the new taxonomy structure.</p>
        
        <div class="migration-preview">
            <h2>Migration Preview</h2>
            <div class="taxonomy-structure">
                <?php foreach ($taxonomy_mapping as $taxonomy => $terms) : ?>
                    <div class="taxonomy-group">
                        <h3><?php echo ucfirst($taxonomy); ?></h3>
                        <?php if (is_array($terms) && isset($terms[0])) : ?>
                            <ul>
                                <?php foreach ($terms as $term) : ?>
                                    <li><?php echo esc_html($term); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else : ?>
                            <ul>
                                <?php foreach ($terms as $parent => $children) : ?>
                                    <li>
                                        <strong><?php echo esc_html($parent); ?></strong>
                                        <ul>
                                            <?php foreach ($children as $child) : ?>
                                                <li><?php echo esc_html($child); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <form method="post" action="">
            <?php wp_nonce_field('taxonomy_migration_nonce'); ?>
            <p class="submit">
                <input type="submit" name="run_migration" class="button button-primary" value="Run Migration">
            </p>
        </form>
    </div>
    <?php
}

// Migration function
function migrate_taxonomies() {
    global $taxonomy_mapping, $term_descriptions, $company_info;
    
    // Create taxonomies if they don't exist
    foreach ($taxonomy_mapping as $taxonomy => $terms) {
        if (!taxonomy_exists($taxonomy)) {
            register_taxonomy($taxonomy, ['deliverable'], [
                'hierarchical' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => ['slug' => $taxonomy],
            ]);
        }
    }
    
    // Migrate terms with descriptions
    foreach ($taxonomy_mapping as $taxonomy => $terms) {
        if (is_array($terms) && isset($terms[0])) {
            // Flat structure (companies and deliverable types)
            foreach ($terms as $term_name) {
                if (!term_exists($term_name, $taxonomy)) {
                    $args = [];
                    if ($taxonomy === 'company' && isset($company_info[$term_name])) {
                        $args['description'] = $company_info[$term_name]['description'];
                        // Store additional company info as term meta
                        if (isset($company_info[$term_name]['industry'])) {
                            add_term_meta($term->term_id, 'industry', $company_info[$term_name]['industry'], true);
                        }
                        if (isset($company_info[$term_name]['website'])) {
                            add_term_meta($term->term_id, 'website', $company_info[$term_name]['website'], true);
                        }
                    } elseif (isset($term_descriptions[$taxonomy][$term_name])) {
                        $args['description'] = $term_descriptions[$taxonomy][$term_name];
                    }
                    $term = wp_insert_term($term_name, $taxonomy, $args);
                    
                    // Add company meta data after term creation
                    if (!is_wp_error($term) && $taxonomy === 'company' && isset($company_info[$term_name])) {
                        if (isset($company_info[$term_name]['industry'])) {
                            add_term_meta($term['term_id'], 'industry', $company_info[$term_name]['industry'], true);
                        }
                        if (isset($company_info[$term_name]['website'])) {
                            add_term_meta($term['term_id'], 'website', $company_info[$term_name]['website'], true);
                        }
                    }
                } else {
                    // Update existing term with description
                    $term = get_term_by('name', $term_name, $taxonomy);
                    if ($term) {
                        if ($taxonomy === 'company' && isset($company_info[$term_name])) {
                            wp_update_term($term->term_id, $taxonomy, [
                                'description' => $company_info[$term_name]['description']
                            ]);
                            // Update company meta data
                            if (isset($company_info[$term_name]['industry'])) {
                                update_term_meta($term->term_id, 'industry', $company_info[$term_name]['industry']);
                            }
                            if (isset($company_info[$term_name]['website'])) {
                                update_term_meta($term->term_id, 'website', $company_info[$term_name]['website']);
                            }
                        } elseif (isset($term_descriptions[$taxonomy][$term_name])) {
                            wp_update_term($term->term_id, $taxonomy, [
                                'description' => $term_descriptions[$taxonomy][$term_name]
                            ]);
                        }
                    }
                }
            }
        } else {
            // Hierarchical structure (technologies and skills)
            foreach ($terms as $parent => $children) {
                // Create parent term if it doesn't exist
                $parent_term = term_exists($parent, $taxonomy);
                if (!$parent_term) {
                    $args = [];
                    if (isset($term_descriptions[$taxonomy][$parent])) {
                        $args['description'] = $term_descriptions[$taxonomy][$parent];
                    }
                    $parent_term = wp_insert_term($parent, $taxonomy, $args);
                } else {
                    // Update parent term with description
                    if (isset($term_descriptions[$taxonomy][$parent])) {
                        wp_update_term($parent_term['term_id'], $taxonomy, [
                            'description' => $term_descriptions[$taxonomy][$parent]
                        ]);
                    }
                }
                
                if (!is_wp_error($parent_term)) {
                    // Create child terms
                    foreach ($children as $child) {
                        if (!term_exists($child, $taxonomy)) {
                            $args = ['parent' => $parent_term['term_id']];
                            if (isset($term_descriptions[$taxonomy][$child])) {
                                $args['description'] = $term_descriptions[$taxonomy][$child];
                            }
                            wp_insert_term($child, $taxonomy, $args);
                        } else {
                            // Update child term with description
                            $child_term = get_term_by('name', $child, $taxonomy);
                            if ($child_term && isset($term_descriptions[$taxonomy][$child])) {
                                wp_update_term($child_term->term_id, $taxonomy, [
                                    'description' => $term_descriptions[$taxonomy][$child]
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
    
    // Update existing posts
    $posts = get_posts([
        'post_type' => 'deliverable',
        'posts_per_page' => -1,
    ]);
    
    foreach ($posts as $post) {
        $existing_tags = wp_get_post_tags($post->ID);
        
        foreach ($existing_tags as $tag) {
            // Try to find matching term in new taxonomies
            foreach ($taxonomy_mapping as $taxonomy => $terms) {
                if (is_array($terms) && isset($terms[0])) {
                    // Check flat structure
                    if (in_array($tag->name, $terms)) {
                        wp_set_object_terms($post->ID, $tag->name, $taxonomy, true);
                    }
                } else {
                    // Check hierarchical structure
                    foreach ($terms as $parent => $children) {
                        if ($tag->name === $parent || in_array($tag->name, $children)) {
                            wp_set_object_terms($post->ID, $tag->name, $taxonomy, true);
                        }
                    }
                }
            }
        }
    }
    
    // Add success message
    add_action('admin_notices', function() {
        echo '<div class="notice notice-success is-dismissible"><p>Taxonomy migration completed successfully!</p></div>';
    });
}

// Add custom columns to company taxonomy
add_filter('manage_edit-company_columns', 'add_company_taxonomy_columns');
function add_company_taxonomy_columns($columns) {
    $new_columns = array();
    
    // Add columns in the desired order
    $new_columns['cb'] = $columns['cb'];
    $new_columns['name'] = $columns['name'];
    $new_columns['logo'] = 'Logo';
    $new_columns['industry'] = 'Industry';
    $new_columns['website'] = 'Website';
    $new_columns['description'] = $columns['description'];
    $new_columns['slug'] = $columns['slug'];
    $new_columns['posts'] = $columns['posts'];
    
    return $new_columns;
}

// Populate custom columns
add_filter('manage_company_custom_column', 'populate_company_taxonomy_columns', 10, 3);
function populate_company_taxonomy_columns($content, $column_name, $term_id) {
    switch ($column_name) {
        case 'logo':
            // Get the logo from ACF if it exists
            $logo = get_field('company_logo', 'company_' . $term_id);
            if ($logo) {
                echo '<img src="' . esc_url($logo['url']) . '" alt="' . esc_attr($logo['alt']) . '" style="max-width: 50px; height: auto;">';
            }
            break;
            
        case 'industry':
            $industry = get_term_meta($term_id, 'industry', true);
            if ($industry) {
                echo esc_html($industry);
            }
            break;
            
        case 'website':
            $website = get_term_meta($term_id, 'website', true);
            if ($website) {
                echo '<a href="' . esc_url($website) . '" target="_blank">' . esc_html($website) . '</a>';
            }
            break;
    }
    return $content;
}

// Make columns sortable
add_filter('manage_edit-company_sortable_columns', 'make_company_columns_sortable');
function make_company_columns_sortable($columns) {
    $columns['industry'] = 'industry';
    $columns['website'] = 'website';
    return $columns;
}

// Add ACF field for company logo if not already added
add_action('acf/init', 'add_company_logo_field');
function add_company_logo_field() {
    if (function_exists('acf_add_local_field_group')) {
        acf_add_local_field_group(array(
            'key' => 'group_company_logo',
            'title' => 'Company Logo',
            'fields' => array(
                array(
                    'key' => 'field_company_logo',
                    'label' => 'Company Logo',
                    'name' => 'company_logo',
                    'type' => 'image',
                    'instructions' => 'Upload the company logo',
                    'required' => 0,
                    'return_format' => 'array',
                    'preview_size' => 'thumbnail',
                    'library' => 'all',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'taxonomy',
                        'operator' => '==',
                        'value' => 'company',
                    ),
                ),
            ),
        ));
    }
} 