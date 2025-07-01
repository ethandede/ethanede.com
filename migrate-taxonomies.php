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
        'Corwin',
        'D-Link',
        'DeWils',
        'DHX Advertising',
        'Experian',
        'Kong',
        'Lightspeed Systems',
        'Liveops',
        'Los Angeles Clippers',
        'Matmarket',
        'NBA',
        'O\'Brien Dental Lab',
        'Pacific Pride',
        'Portland Japanese Garden',
        'Product Channels',
        'Quiksilver',
        'Rehau',
        'Staples',
        'TigerStop',
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
        'Sound Design',
        'Wireframes',
        'Sitemaps',
        'Excel/Spreadsheet'
    ],
    'technology' => [
        'Creative Software' => [
            'Adobe Creative Cloud',
            'After Effects',
            'Figma',
            'Sketch',
            'Pro Tools',
            'Logic Pro',
            'Ableton Live',
            'OmniGraffle'
        ],
        'Development Tools' => [
            'Visual Studio Code',
            'GitHub',
            'Advanced Custom Fields'
        ],
        'Business Platforms' => [
            'WordPress',
            'Salesforce',
            'Pardot',
            'Drift',
            'Vidyard',
            'VWO',
            'Microsoft Office'
        ],
        'Project Management Tools' => [
            'Jira',
            'Monday',
            'Trello'
        ],
        'Analytics Platforms' => [
            'Google Analytics',
            'Google Tag Manager',
            'Looker Studio',
            'Piwik Pro',
            'Semrush'
        ],
        'AI Tools' => [
            'ChatGPT',
            'Grok',
            'Midjourney',
            'Cursor'
        ]
    ],
    'skill' => [
        'Technical Abilities' => [
            'HTML',
            'CSS',
            'JavaScript',
            'PHP',
            'Sass',
            'Web Development',
            'Database Management',
            'API Integration'
        ],
        'Design Competencies' => [
            'UX Design',
            'UI Design',
            'Graphic Design',
            'Visual Design',
            'Brand Design',
            'Typography',
            'Color Theory',
            'Layout Design',
            'Iconography',
            'Wireframing',
            'Prototyping'
        ],
        'Creative Abilities' => [
            'Video Production',
            'Animation',
            'Motion Graphics',
            'Music Production',
            'Sound Design',
            'Audio Engineering'
        ],
        'Business Methodologies' => [
            'Project Management',
            'SEO',
            'CRO',
            'Data Analysis',
            'Content Strategy',
            'Digital Marketing',
            'User Research',
            'A/B Testing'
        ],
        'Technical Methodologies' => [
            'Responsive Design',
            'Performance Optimization',
            'Accessibility',
            'Version Control',
            'Testing',
            'Deployment',
            'Hosting Management',
            'Security Best Practices'
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
        'Sound Design' => 'Creation and manipulation of audio elements for enhanced user experience and storytelling.',
        'Wireframes' => 'Structural blueprints and layout frameworks defining the skeletal structure of web pages and applications.',
        'Sitemaps' => 'Information architecture diagrams outlining site structure, navigation paths, and content organization.',
        'Excel/Spreadsheet' => 'Interactive data tables and spreadsheets with filtering, search, and export capabilities for data analysis and reporting.'
    ],
    'technology' => [
        'Creative Software' => 'Professional software applications for design, video, audio, and visual content creation.',
        'Adobe Creative Cloud' => 'Industry-standard creative software suite for design, video, and digital content creation.',
        'After Effects' => 'Professional motion graphics and visual effects software for creating dynamic animations.',
        'Figma' => 'Collaborative interface design tool for creating and prototyping digital experiences.',
        'Sketch' => 'Vector-based design tool for creating user interfaces and digital graphics.',
        'Pro Tools' => 'Professional digital audio workstation for music and sound production.',
        'Logic Pro' => 'Advanced music production and audio editing software.',
        'Ableton Live' => 'Creative music production and performance software.',
        'OmniGraffle' => 'Professional diagramming and visual communication tool for creating wireframes, flowcharts, and organizational charts.',
        'Development Tools' => 'Software applications and platforms for coding, version control, and development workflows.',
        'Visual Studio Code' => 'Modern code editor with powerful features for web development.',
        'GitHub' => 'Platform for version control and collaborative software development.',
        'Advanced Custom Fields' => 'WordPress plugin for customizing content management.',
        'Business Platforms' => 'Enterprise and business software systems for operations, marketing, and content management.',
        'WordPress' => 'Flexible content management system for building and managing websites.',
        'Salesforce' => 'Customer relationship management platform for business operations.',
        'Pardot' => 'Marketing automation platform for B2B marketing and sales alignment.',
        'Drift' => 'Conversational marketing platform for real-time customer engagement.',
        'Vidyard' => 'Video platform for business marketing and sales.',
        'VWO' => 'Conversion rate optimization and A/B testing platform.',
        'Microsoft Office' => 'Comprehensive productivity software suite for document creation, data analysis, and presentation design.',
        'Project Management Tools' => 'Software applications for project organization, task management, and team collaboration.',
        'Jira' => 'Agile project management tool for software development teams.',
        'Monday' => 'Visual project management platform for team collaboration.',
        'Trello' => 'Flexible project organization tool using boards and cards.',
        'Analytics Platforms' => 'Data analysis and business intelligence software for tracking performance and insights.',
        'Google Analytics' => 'Web analytics service for tracking and reporting website traffic.',
        'Google Tag Manager' => 'Tag management system for deploying marketing and analytics tags.',
        'Looker Studio' => 'Data visualization and business intelligence platform.',
        'Piwik Pro' => 'Privacy-focused analytics platform for data-driven insights.',
        'Semrush' => 'Comprehensive SEO and digital marketing analytics platform for keyword research, competitor analysis, and search optimization.',
        'AI Tools' => 'Artificial intelligence software and platforms for content creation and development assistance.',
        'ChatGPT' => 'Advanced language model for natural language processing and content generation.',
        'Grok' => 'AI assistant for data analysis and insights generation.',
        'Midjourney' => 'AI-powered image generation tool for creating unique visual content.',
        'Cursor' => 'AI-enhanced code editor for intelligent development assistance.'
    ],
    'skill' => [
        'Technical Abilities' => 'Core programming and development competencies for building digital solutions.',
        'HTML' => 'Markup language proficiency for structuring web content and semantic elements.',
        'CSS' => 'Styling language expertise for web design, layout, and responsive interfaces.',
        'JavaScript' => 'Programming language mastery for interactive web development and dynamic functionality.',
        'PHP' => 'Server-side scripting language capability for web application development.',
        'Sass' => 'CSS preprocessor proficiency for enhanced styling workflows and maintainable code.',
        'Web Development' => 'Full-stack development ability encompassing front-end and back-end technologies.',
        'Database Management' => 'Competency in designing, maintaining, and optimizing database systems.',
        'API Integration' => 'Ability to connect and integrate third-party services and data sources.',
        'Design Competencies' => 'Visual design abilities for creating effective and aesthetically pleasing user experiences.',
        'UX Design' => 'User experience design methodology focusing on user research, testing, and optimization.',
        'UI Design' => 'User interface design capability for creating intuitive and functional digital interfaces.',
        'Graphic Design' => 'Visual communication ability through typography, imagery, and layout composition.',
        'Visual Design' => 'Aesthetic design competency for creating cohesive visual systems and brand experiences.',
        'Brand Design' => 'Brand identity development methodology including logo design and visual language creation.',
        'Typography' => 'Expertise in type selection, hierarchy, and readability for effective communication.',
        'Color Theory' => 'Understanding of color psychology, harmony, and strategic application in design.',
        'Layout Design' => 'Spatial arrangement ability for organizing visual elements effectively.',
        'Iconography' => 'Symbol and icon design competency for visual communication systems.',
        'Wireframing' => 'Information architecture ability for creating structural blueprints of interfaces.',
        'Prototyping' => 'Interactive model development for testing user flows and functionality.',
        'Creative Abilities' => 'Artistic and multimedia production competencies for engaging content creation.',
        'Video Production' => 'End-to-end video creation ability including filming, editing, and post-production.',
        'Animation' => 'Motion graphics and animated content creation capability.',
        'Motion Graphics' => 'Dynamic visual effects and animated graphic design competency.',
        'Music Production' => 'Audio composition and production ability for multimedia projects.',
        'Sound Design' => 'Audio element creation and manipulation for enhanced user experiences.',
        'Audio Engineering' => 'Technical sound recording, mixing, and mastering competency.',
        'Business Methodologies' => 'Strategic approaches and processes for successful project delivery and business growth.',
        'Project Management' => 'Methodology for strategic planning, execution, and delivery of digital projects.',
        'SEO' => 'Search engine optimization methodology for improving online visibility and rankings.',
        'CRO' => 'Conversion rate optimization process for enhancing user engagement and business outcomes.',
        'Data Analysis' => 'Analytical methodology for interpreting metrics and driving data-informed decisions.',
        'Content Strategy' => 'Strategic approach to content planning, creation, and distribution.',
        'Digital Marketing' => 'Comprehensive methodology for online marketing campaigns and audience engagement.',
        'User Research' => 'Research methodology for understanding user needs, behaviors, and preferences.',
        'A/B Testing' => 'Experimental methodology for comparing and optimizing user experiences.',
        'Technical Methodologies' => 'Best practices and approaches for technical implementation and optimization.',
        'Responsive Design' => 'Design methodology for creating adaptable interfaces across multiple devices.',
        'Performance Optimization' => 'Technical approach to improving website speed, efficiency, and user experience.',
        'Accessibility' => 'Inclusive design methodology ensuring digital products are usable by all users.',
        'Version Control' => 'Code management methodology for tracking changes and collaborative development.',
        'Testing' => 'Quality assurance methodology for ensuring functionality and user experience.',
        'Deployment' => 'Technical process for launching and maintaining web applications.',
        'Hosting Management' => 'Server administration and infrastructure management competency.',
        'Security Best Practices' => 'Methodology for implementing secure coding and data protection measures.'
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
    'Corwin' => [
        'description' => 'Beverage company acquired by longtime partner PepsiCo in 2023, specializing in innovative drink solutions.',
        'industry' => 'Food & Beverage',
        'website' => ''
    ],
    'D-Link' => [
        'description' => 'Global leader in networking and connectivity solutions for consumers and businesses.',
        'industry' => 'Networking & Technology',
        'website' => 'https://www.dlink.com'
    ],
    'DeWils' => [
        'description' => 'Family-owned custom cabinetry company with over sixty years of craftsmanship, combining traditional woodworking with modern technologies.',
        'industry' => 'Home Improvement & Manufacturing',
        'website' => 'https://www.dewils.com'
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
    'Matmarket' => [
        'description' => 'Global supplier of technologies for footwear and gloves, providing innovative materials and component solutions with over 200 factory partners worldwide.',
        'industry' => 'Manufacturing & Technology',
        'website' => 'https://www.matmarket.com'
    ],
    'NBA' => [
        'description' => 'National Basketball Association, the premier professional basketball league in North America.',
        'industry' => 'Sports & Entertainment',
        'website' => 'https://www.nba.com'
    ],
    'O\'Brien Dental Lab' => [
        'description' => 'Family-owned dental laboratory providing clinically exceptional crowns, bridges, removables and implants with ISO 9001:2015 certification since 1969.',
        'industry' => 'Healthcare & Dental Services',
        'website' => 'https://obriendentallab.com'
    ],
    'Pacific Pride' => [
        'description' => 'Leading fuel card services provider offering commercial fueling solutions, part of CORPAY global business payments company.',
        'industry' => 'Energy & Financial Services',
        'website' => 'https://pacificpride.com'
    ],
    'Portland Japanese Garden' => [
        'description' => 'Cultural institution featuring traditional Japanese gardens and cultural programming.',
        'industry' => 'Cultural Arts',
        'website' => 'https://japanesegarden.org'
    ],
    'Product Channels' => [
        'description' => 'Product Channels offered a SaaS solution for streamlined product marketing and syndication, delivering branded content, current product data, and rich media (3D 360, HD video) via an iframe for easy integration on major retail and eCommerce platforms like Amazon, eBay, Best Buy, and Staples.',
        'industry' => 'SaaS & eCommerce Technology',
        'website' => ''
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
    'TigerStop' => [
        'description' => 'Manufacturing machinery and software solutions provider specializing in automated positioning systems for cutting, measuring, and material handling across multiple industries.',
        'industry' => 'Manufacturing Equipment & Industrial Technology',
        'website' => 'https://www.tigerstop.com'
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
    
    // Handle data recovery
    if (isset($_POST['recover_data']) && check_admin_referer('data_recovery_nonce')) {
        recover_company_data();
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
        
        <div class="data-recovery-section" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #ccc;">
            <h2>🔄 Data Recovery Tool</h2>
            <p><strong>Use this if you're missing company URLs or logos after field structure changes.</strong></p>
            <p>This tool will:</p>
            <ul style="margin-left: 20px;">
                <li>Restore website URLs from existing term meta or predefined company data</li>
                <li>Automatically link company logos from your Media Library based on filename patterns</li>
                <li>Show a detailed report of what was recovered</li>
            </ul>
            
            <form method="post" action="" style="margin-top: 1rem;">
                <?php wp_nonce_field('data_recovery_nonce'); ?>
                <p class="submit">
                    <input type="submit" name="recover_data" class="button button-secondary" value="🔄 Recover Company Data" onclick="return confirm('This will attempt to restore missing company URLs and logos. Continue?');">
                </p>
            </form>
        </div>
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
                        // Store company website in ACF field
                        if (isset($company_info[$term_name]['website'])) {
                            update_field('company_website', $company_info[$term_name]['website'], 'company_' . $term->term_id);
                        }
                    } elseif (isset($term_descriptions[$taxonomy][$term_name])) {
                        $args['description'] = $term_descriptions[$taxonomy][$term_name];
                    }
                    $term = wp_insert_term($term_name, $taxonomy, $args);
                    
                    // Add company ACF data after term creation
                    if (!is_wp_error($term) && $taxonomy === 'company' && isset($company_info[$term_name])) {
                        if (isset($company_info[$term_name]['website'])) {
                            update_field('company_website', $company_info[$term_name]['website'], 'company_' . $term['term_id']);
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
                            // Update company ACF data
                            if (isset($company_info[$term_name]['website'])) {
                                update_field('company_website', $company_info[$term_name]['website'], 'company_' . $term->term_id);
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
    
    // Update existing deliverables
    $deliverables = get_posts([
        'post_type' => 'deliverable',
        'posts_per_page' => -1,
    ]);
    
    foreach ($deliverables as $deliverable) {
        $existing_tags = wp_get_post_tags($deliverable->ID);
        
        foreach ($existing_tags as $tag) {
            // Try to find matching term in new taxonomies
            foreach ($taxonomy_mapping as $taxonomy => $terms) {
                if (is_array($terms) && isset($terms[0])) {
                    // Check flat structure
                    if (in_array($tag->name, $terms)) {
                        wp_set_object_terms($deliverable->ID, $tag->name, $taxonomy, true);
                    }
                } else {
                    // Check hierarchical structure
                    foreach ($terms as $parent => $children) {
                        if ($tag->name === $parent || in_array($tag->name, $children)) {
                            wp_set_object_terms($deliverable->ID, $tag->name, $taxonomy, true);
                        }
                    }
                }
            }
        }
        
        // Set company taxonomy for deliverables based on related project
        $related_project = get_field('related_project', $deliverable->ID);
        if ($related_project) {
            $project_companies = get_the_terms($related_project, 'company');
            if ($project_companies && !is_wp_error($project_companies)) {
                $company_ids = wp_list_pluck($project_companies, 'term_id');
                wp_set_object_terms($deliverable->ID, $company_ids, 'company', false);
            }
        }
    }
    
    // Update existing projects
    $projects = get_posts([
        'post_type' => 'project',
        'posts_per_page' => -1,
    ]);
    
    foreach ($projects as $project) {
        $existing_tags = wp_get_post_tags($project->ID);
        
        foreach ($existing_tags as $tag) {
            // Try to find matching term in new taxonomies
            foreach ($taxonomy_mapping as $taxonomy => $terms) {
                if (is_array($terms) && isset($terms[0])) {
                    // Check flat structure
                    if (in_array($tag->name, $terms)) {
                        wp_set_object_terms($project->ID, $tag->name, $taxonomy, true);
                    }
                } else {
                    // Check hierarchical structure
                    foreach ($terms as $parent => $children) {
                        if ($tag->name === $parent || in_array($tag->name, $children)) {
                            wp_set_object_terms($project->ID, $tag->name, $taxonomy, true);
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
            
        case 'website':
            $website = get_field('company_website', 'company_' . $term_id);
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
    $columns['website'] = 'website';
    return $columns;
}

// ACF field group for company logo is now handled by acf-json/group_company.json 

/**
 * Data Recovery Function - Restore Company URLs and Link Logos
 * This function recovers data that may have been lost during field structure changes
 */
function recover_company_data() {
    global $company_info;
    
    // Get all company terms
    $companies = get_terms([
        'taxonomy' => 'company',
        'hide_empty' => false,
    ]);
    
    if (empty($companies) || is_wp_error($companies)) {
        return;
    }
    
    $recovery_log = [];
    
    foreach ($companies as $company) {
        $company_name = $company->name;
        $company_id = $company->term_id;
        $updates_made = [];
        
        // 1. Recover website URL from term meta if ACF field is empty
        $current_acf_website = get_field('company_website', 'company_' . $company_id);
        if (empty($current_acf_website)) {
            // Check term meta first
            $meta_website = get_term_meta($company_id, 'website', true);
            if (!empty($meta_website)) {
                update_field('company_website', $meta_website, 'company_' . $company_id);
                $updates_made[] = "Website URL restored from term meta: $meta_website";
            } else {
                // Fallback to predefined company info
                if (isset($company_info[$company_name]['website']) && !empty($company_info[$company_name]['website'])) {
                    update_field('company_website', $company_info[$company_name]['website'], 'company_' . $company_id);
                    $updates_made[] = "Website URL set from company info: " . $company_info[$company_name]['website'];
                }
            }
        }
        
        // 2. Auto-link company logo from media library if ACF field is empty
        $current_acf_logo = get_field('company_logo', 'company_' . $company_id);
        if (empty($current_acf_logo)) {
            // Create search patterns for the company name
            $search_patterns = [
                'logo_' . strtolower(str_replace([' ', '-', '&', '\''], ['', '', '', ''], $company_name)),
                'logo_' . strtolower(str_replace(' ', '', $company_name)),
                strtolower(str_replace([' ', '-', '&', '\''], ['', '', '', ''], $company_name)) . '_logo',
                strtolower(str_replace(' ', '', $company_name)) . '_logo',
                strtolower($company_name),
            ];
            
            // Special cases for known company logo patterns
            $special_cases = [
                'Act-On' => 'logo_actOn',
                'Best Buy' => 'logo_bestBuy',
                'D-Link' => 'logo_d-link',
                'Los Angeles Clippers' => 'logo_laClippers',
                'Portland Japanese Garden' => 'logo_portlandJapaneseGarden',
                'O\'Brien Dental Lab' => 'logo_obrienDentalLab',
                'DHX Advertising' => 'logo_dhxAdvertising',
                'Lightspeed Systems' => 'logo_lightspeedSystems',
            ];
            
            if (isset($special_cases[$company_name])) {
                $search_patterns = array_merge([$special_cases[$company_name]], $search_patterns);
            }
            
            // Search for logo in media library
            $logo_found = false;
            foreach ($search_patterns as $pattern) {
                $attachments = get_posts([
                    'post_type' => 'attachment',
                    'post_status' => 'inherit',
                    'posts_per_page' => 1,
                    'meta_query' => [
                        [
                            'key' => '_wp_attached_file',
                            'value' => $pattern,
                            'compare' => 'LIKE'
                        ]
                    ]
                ]);
                
                if (!empty($attachments)) {
                    $attachment_id = $attachments[0]->ID;
                    update_field('company_logo', $attachment_id, 'company_' . $company_id);
                    $updates_made[] = "Logo linked from media library: " . get_attached_file($attachment_id);
                    $logo_found = true;
                    break;
                }
            }
            
            // Alternative search by filename in title or filename
            if (!$logo_found) {
                foreach ($search_patterns as $pattern) {
                    $attachments = get_posts([
                        'post_type' => 'attachment',
                        'post_status' => 'inherit', 
                        'posts_per_page' => 1,
                        's' => $pattern,
                    ]);
                    
                    if (!empty($attachments)) {
                        $attachment_id = $attachments[0]->ID;
                        update_field('company_logo', $attachment_id, 'company_' . $company_id);
                        $updates_made[] = "Logo linked from media library (filename search): " . $attachments[0]->post_title;
                        $logo_found = true;
                        break;
                    }
                }
            }
            
            if (!$logo_found) {
                $updates_made[] = "No logo found in media library for patterns: " . implode(', ', array_slice($search_patterns, 0, 3));
            }
        }
        
        if (!empty($updates_made)) {
            $recovery_log[$company_name] = $updates_made;
        }
    }
    
    // Display recovery results
    if (!empty($recovery_log)) {
        add_action('admin_notices', function() use ($recovery_log) {
            echo '<div class="notice notice-success is-dismissible">';
            echo '<h3>Company Data Recovery Results:</h3>';
            foreach ($recovery_log as $company_name => $updates) {
                echo '<p><strong>' . esc_html($company_name) . ':</strong></p>';
                echo '<ul style="margin-left: 20px;">';
                foreach ($updates as $update) {
                    echo '<li>' . esc_html($update) . '</li>';
                }
                echo '</ul>';
            }
            echo '</div>';
        });
    } else {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-info is-dismissible"><p>No company data needed recovery - all fields appear to be populated.</p></div>';
        });
    }
} 