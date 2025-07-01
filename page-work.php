<?php
/*
Template Name: Work Archive
*/

// Always fetch all projects and deliverables for the work grid
$all_projects = get_posts([
    'post_type' => 'project',
    'posts_per_page' => -1,
    'post_status' => 'publish'
]);
$all_deliverables = get_posts([
    'post_type' => 'deliverable',
    'posts_per_page' => -1,
    'post_status' => 'publish'
]);

// Set custom page title for work archive
add_filter('wp_title', function($title) {
    return 'My Work - Projects & Deliverables | ' . get_bloginfo('name');
});

// Set custom meta description
add_action('wp_head', function() {
    echo '<meta name="description" content="Explore my complete portfolio of web development projects and deliverables - from strategy and design to development and digital solutions.">' . "\n";
});

get_header();
?>

<main id="work-archive">

    <!-- Work Header Section -->
    <section class="work-header">
        <div class="container">
            <h1>My Work</h1>
            <p class="supporting-text">Explore a portfolio of select projects and deliverables I've had the pleasure to be involved with - from strategy and design to development and digital solutions.</p>
        </div>
    </section>

    <!-- Work Grid Section with Sidebar -->
    <section class="work-grid">
        <div class="container">
            <div class="work-layout">
                <!-- Filter sidebar removed for now. Restore from git history or previous code when needed. -->

                <!-- Work Grid -->
                <main class="work-content">
                    <div class="work-list">
                        <?php
                        // Combine all projects and deliverables
                        $all_work = array_merge($all_projects, $all_deliverables);
                        
                        // Sort: projects first, then deliverables, then by date (newest first) within each type
                        usort($all_work, function($a, $b) {
                            // First, prioritize projects over deliverables
                            $a_is_project = ($a->post_type === 'project');
                            $b_is_project = ($b->post_type === 'project');
                            
                            if ($a_is_project && !$b_is_project) {
                                return -1; // Project comes first
                            } elseif (!$a_is_project && $b_is_project) {
                                return 1; // Deliverable comes after
                            } else {
                                // Both are same type, sort by date (newest first)
                                return strtotime($b->post_date) - strtotime($a->post_date);
                            }
                        });
                        
                        if ($all_work) :
                            foreach ($all_work as $work_item) :
                                $is_project = ($work_item->post_type === 'project');
                                
                                // Get the first image and excerpt
                                $first_image = null;
                                $excerpt = '';
                                $available_media_types = [];
                                
                                if ($is_project) {
                                    // For projects, get featured media (now image-only)
                                    $featured_media = get_field('featured_media', $work_item->ID);
                                    if ($featured_media) {
                                        $first_image = [
                                            'url' => $featured_media,
                                            'alt' => esc_attr($work_item->post_title)
                                        ];
                                    }
                                    
                                    // If no featured_media, check for WordPress featured image
                                    if (!$first_image && has_post_thumbnail($work_item->ID)) {
                                        $first_image = [
                                            'url' => get_the_post_thumbnail_url($work_item->ID, 'medium'),
                                            'alt' => get_post_meta(get_post_thumbnail_id($work_item->ID), '_wp_attachment_image_alt', true)
                                        ];
                                    }
                                    
                                    // Get project description for excerpt
                                    $project_description = get_field('project_description', $work_item->ID);
                                    if ($project_description) {
                                        $excerpt = wp_trim_words(strip_tags($project_description), 25, '...');
                                    }
                                    
                                    // For projects, aggregate media types from all related deliverables
                                    $project_deliverables = get_field('project_deliverables', $work_item->ID);
                                    if ($project_deliverables) {
                                        foreach ($project_deliverables as $deliverable) {
                                            // Check deliverable media
                                            $deliverable_media = get_field('deliverable_media', $deliverable->ID);
                                            if ($deliverable_media) {
                                                foreach ($deliverable_media as $media_item) {
                                                    $file_type = wp_check_filetype($media_item['url']);
                                                    if (strpos($file_type['type'], 'image') !== false && !in_array('image', $available_media_types)) {
                                                        $available_media_types[] = 'image';
                                                    } elseif (strpos($file_type['type'], 'video') !== false && !in_array('video', $available_media_types)) {
                                                        $available_media_types[] = 'video';
                                                    }
                                                }
                                            }
                                            
                                            // Check deliverable PDF
                                            $deliverable_pdf = get_field('deliverable_pdf', $deliverable->ID);
                                            if ($deliverable_pdf && !in_array('pdf', $available_media_types)) {
                                                $available_media_types[] = 'pdf';
                                            }
                                        }
                                    }
                                } else {
                                    // For deliverables, check for deliverable featured image first
                                    $deliverable_featured = get_field('deliverable_featured_image', $work_item->ID);
                                    if ($deliverable_featured) {
                                        $first_image = [
                                            'url' => $deliverable_featured,
                                            'alt' => esc_attr($work_item->post_title)
                                        ];
                                    }
                                    
                                    // If no deliverable featured image, get first media item
                                    if (!$first_image) {
                                        $media = get_field('deliverable_media', $work_item->ID);
                                        if ($media) {
                                            foreach ($media as $item) {
                                                if ($item['type'] === 'image') {
                                                    $first_image = [
                                                        'url' => $item['sizes']['medium'],
                                                        'alt' => $item['alt']
                                                    ];
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                    
                                    // If no image found in deliverable_featured_image or deliverable_media, check for WordPress featured image
                                    if (!$first_image && has_post_thumbnail($work_item->ID)) {
                                        $first_image = [
                                            'url' => get_the_post_thumbnail_url($work_item->ID, 'medium'),
                                            'alt' => get_post_meta(get_post_thumbnail_id($work_item->ID), '_wp_attachment_image_alt', true)
                                        ];
                                    }
                                    
                                    // Get deliverable excerpt
                                    $deliverable_excerpt = get_field('deliverable_excerpt', $work_item->ID);
                                    if ($deliverable_excerpt) {
                                        $excerpt = $deliverable_excerpt;
                                    } elseif ($work_item->post_excerpt) {
                                        $excerpt = $work_item->post_excerpt;
                                    } else {
                                        // Fallback to description if no excerpt
                                        $deliverable_description = get_field('deliverable_description', $work_item->ID);
                                        if ($deliverable_description) {
                                            $excerpt = wp_trim_words(strip_tags($deliverable_description), 25, '...');
                                        }
                                    }
                                    
                                    // For deliverables, check direct media types
                                    $deliverable_media = get_field('deliverable_media', $work_item->ID);
                                    if ($deliverable_media) {
                                        foreach ($deliverable_media as $media_item) {
                                            $file_type = wp_check_filetype($media_item['url']);
                                            if (strpos($file_type['type'], 'image') !== false && !in_array('image', $available_media_types)) {
                                                $available_media_types[] = 'image';
                                            } elseif (strpos($file_type['type'], 'video') !== false && !in_array('video', $available_media_types)) {
                                                $available_media_types[] = 'video';
                                            }
                                        }
                                    }
                                    
                                    // Check for PDF
                                    $deliverable_pdf = get_field('deliverable_pdf', $work_item->ID);
                                    if ($deliverable_pdf && !in_array('pdf', $available_media_types)) {
                                        $available_media_types[] = 'pdf';
                                    }
                                }
                                
                                // Get related project for deliverables, or self ID for projects
                                $project_ids = [];
                                if ($is_project) {
                                    // For projects, use their own ID
                                    $project_ids = [$work_item->ID];
                                } else {
                                    // For deliverables, get related project
                                    $related_project = get_field('related_project', $work_item->ID);
                                    if ($related_project) {
                                        if (is_array($related_project) && !empty($related_project)) {
                                            if (is_object($related_project[0]) && isset($related_project[0]->ID)) {
                                                $project_ids = wp_list_pluck($related_project, 'ID');
                                            } else {
                                                $project_ids = $related_project;
                                            }
                                        } elseif (is_object($related_project) && isset($related_project->ID)) {
                                            $project_ids = [$related_project->ID];
                                        } elseif (is_numeric($related_project) || is_string($related_project)) {
                                            $project_ids = [$related_project];
                                        }
                                    }
                                }
                                $project_ids = array_filter(array_map('strval', $project_ids));
                                
                                // Get tools
                                $technologies = get_the_terms($work_item->ID, 'technology');
                                $tech_slugs = [];
                                if ($technologies && !is_wp_error($technologies) && is_array($technologies)) {
                                    $tech_slugs = wp_list_pluck($technologies, 'slug');
                                }
                                $tech_slugs = array_filter(array_map('strval', (array)$tech_slugs));
                                
                                // Get companies
                                $companies = get_the_terms($work_item->ID, 'company');
                                $company_slugs = [];
                                if ($companies && !is_wp_error($companies) && is_array($companies)) {
                                    $company_slugs = wp_list_pluck($companies, 'slug');
                                }
                                $company_slugs = array_filter(array_map('strval', (array)$company_slugs));
                                
                                // Get type info for deliverables
                                $type_term = null;
                                $deliverable_type_slugs = [];
                                if (!$is_project) {
                                    $type_terms = get_the_terms($work_item->ID, 'deliverable_type');
                                    if ($type_terms && !is_wp_error($type_terms)) {
                                        $type_term = $type_terms[0];
                                        $deliverable_type_slugs = wp_list_pluck($type_terms, 'slug');
                                    }
                                }
                                $deliverable_type_slugs = array_filter(array_map('strval', (array)$deliverable_type_slugs));
                                // Prepare data for master card system
                                $image_url = $first_image ? $first_image['url'] : '';
                                $image_alt = $first_image ? $first_image['alt'] : $work_item->post_title;
                                
                                // Prepare tags
                                $tags = [];
                                if ($is_project) {
                                    $tags[] = 'Project';
                                } elseif ($type_term) {
                                    $display_name = $type_term->name;
                                    // Convert plural forms to singular for card display
                                    if ($display_name === 'Microsites') {
                                        $display_name = 'Microsite';
                                    } elseif ($display_name === 'Animations') {
                                        $display_name = 'Animation';
                                    }
                                    $tags[] = $display_name;
                                }
                                
                                // Determine type for master card system
                                $card_type = $is_project ? 'project' : 'deliverable';
                                
                                // Use unified master card system with data attributes
                                $card_extra_classes = ['card--' . $card_type, 'card--work'];
                                $card_data_attrs = [
                                    'data-content-type' => implode(',', $deliverable_type_slugs), // Now stores deliverable types
                                    'data-format' => $is_project ? 'project' : 'deliverable', // New attribute for format
                                    'data-projects' => implode(',', $project_ids),
                                    'data-technologies' => implode(',', $tech_slugs),
                                    'data-companies' => implode(',', $company_slugs)
                                ];
                                
                                if ($is_project) {
                                    ee_render_project_card($work_item->ID, 'work', [
                                        'image_url' => $image_url,
                                        'image_alt' => $image_alt,
                                        'title' => $work_item->post_title,
                                        'description' => $excerpt,
                                        'tags' => $tags,
                                        'show_media_types' => !empty($available_media_types),
                                        'media_types' => $available_media_types,
                                        'show_company' => true,
                                        'extra_classes' => $card_extra_classes,
                                        'data_attributes' => $card_data_attrs
                                    ]);
                                } else {
                                    ee_render_deliverable_card($work_item->ID, 'work', [
                                        'image_url' => $image_url,
                                        'image_alt' => $image_alt,
                                        'title' => $work_item->post_title,
                                        'description' => $excerpt,
                                        'tags' => $tags,
                                        'show_media_types' => !empty($available_media_types),
                                        'media_types' => $available_media_types,
                                        'show_company' => true,
                                        'extra_classes' => $card_extra_classes,
                                        'data_attributes' => $card_data_attrs
                                    ]);
                                }
                                ?>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p class="no-work-found">No work found.</p>
                        <?php endif; ?>
                    </div>
                </main>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?> 