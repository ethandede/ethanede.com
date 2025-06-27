<?php
/*
Template Name: Work Archive
*/

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

    <!-- Hero Section -->
    <section class="work-header">
        <div class="container">
            <h1>My Work</h1>
            <p class="supporting-text">Explore my complete portfolio of projects and deliverables - from strategy and design to development and digital solutions.</p>
            
            <!-- Filter Controls -->
            <div class="work-filters">
                <div class="filter-controls">
                    <!-- Filter Toggle Button -->
                    <button type="button" id="filter-toggle" class="filter-toggle" aria-expanded="false">
                        <i class="far fa-filter"></i>
                        <i class="far fa-chevron-down filter-toggle-icon"></i>
                    </button>
                    <!-- Inline Reset Button -->
                    <button type="button" id="reset-filters-inline" class="filter-reset-inline">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Collapsible Filter Form -->
                <div class="filter-form" id="filter-form" aria-hidden="true">
                    <?php
                    // Get all published projects and deliverables for filter building
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

                    // Content Type Filter - Get deliverable types used by deliverables
                    $used_deliverable_type_ids = [];
                    
                    // Collect from deliverables only
                    foreach ($all_deliverables as $deliverable) {
                        $deliverable_types = get_the_terms($deliverable->ID, 'deliverable_type');
                        if ($deliverable_types && !is_wp_error($deliverable_types)) {
                            foreach ($deliverable_types as $type) {
                                $used_deliverable_type_ids[] = $type->term_id;
                            }
                        }
                    }
                    
                    $deliverable_types = [];
                    if (!empty($used_deliverable_type_ids)) {
                        $deliverable_types = get_terms([
                            'taxonomy' => 'deliverable_type',
                            'include' => array_unique($used_deliverable_type_ids),
                            'orderby' => 'name',
                            'order' => 'ASC'
                        ]);
                    }
                    
                    if ($deliverable_types && !is_wp_error($deliverable_types)) : ?>
                        <div class="filter-group">
                            <label for="content-type-filter"><i class="fas fa-th-list"></i> Content Type</label>
                            <select id="content-type-filter">
                                <option value="">All Content Types</option>
                                <?php foreach ($deliverable_types as $type) : ?>
                                    <option value="<?php echo esc_attr($type->slug); ?>">
                                        <?php echo esc_html($type->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <!-- Format Filter -->
                    <div class="filter-group">
                        <label for="format-filter"><i class="fas fa-layer-group"></i> Format</label>
                        <select id="format-filter">
                            <option value="">All Work</option>
                            <option value="project">Projects Only</option>
                            <option value="deliverable">Deliverables Only</option>
                        </select>
                    </div>
                    
                    <?php
                    // Project Filter (only show if we have projects)
                    if ($all_projects) : ?>
                        <div class="filter-group">
                            <label for="project-filter"><i class="fas fa-folder"></i> Project</label>
                            <select id="project-filter">
                                <option value="">All Projects</option>
                                <?php foreach ($all_projects as $project) : ?>
                                    <option value="<?php echo esc_attr($project->ID); ?>">
                                        <?php echo esc_html($project->post_title); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php
                    // Tool Filter - Get tools used by both projects and deliverables
                    $used_tech_ids = [];
                    
                    // Collect from projects
                    foreach ($all_projects as $project) {
                        $project_techs = get_the_terms($project->ID, 'technology');
                        if ($project_techs && !is_wp_error($project_techs)) {
                            foreach ($project_techs as $tech) {
                                $used_tech_ids[] = $tech->term_id;
                            }
                        }
                    }
                    
                    // Collect from deliverables
                    foreach ($all_deliverables as $deliverable) {
                        $deliverable_techs = get_the_terms($deliverable->ID, 'technology');
                        if ($deliverable_techs && !is_wp_error($deliverable_techs)) {
                            foreach ($deliverable_techs as $tech) {
                                $used_tech_ids[] = $tech->term_id;
                            }
                        }
                    }
                    
                    $technologies = [];
                    if (!empty($used_tech_ids)) {
                        $technologies = get_terms([
                            'taxonomy' => 'technology',
                            'include' => array_unique($used_tech_ids),
                            'orderby' => 'name',
                            'order' => 'ASC'
                        ]);
                    }
                    
                    if ($technologies && !is_wp_error($technologies)) : ?>
                        <div class="filter-group">
                            <label for="technology-filter"><i class="fas fa-cogs"></i> Tool</label>
                            <select id="technology-filter">
                                <option value="">All Tools</option>
                                <?php foreach ($technologies as $tech) : ?>
                                    <option value="<?php echo esc_attr($tech->slug); ?>">
                                        <?php echo esc_html($tech->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php
                    // Company Filter - Get companies used by both projects and deliverables
                    $used_company_ids = [];
                    
                    // Collect from projects
                    foreach ($all_projects as $project) {
                        $project_companies = get_the_terms($project->ID, 'company');
                        if ($project_companies && !is_wp_error($project_companies)) {
                            foreach ($project_companies as $company) {
                                $used_company_ids[] = $company->term_id;
                            }
                        }
                    }
                    
                    // Collect from deliverables
                    foreach ($all_deliverables as $deliverable) {
                        $deliverable_companies = get_the_terms($deliverable->ID, 'company');
                        if ($deliverable_companies && !is_wp_error($deliverable_companies)) {
                            foreach ($deliverable_companies as $company) {
                                $used_company_ids[] = $company->term_id;
                            }
                        }
                    }
                    
                    $companies = [];
                    if (!empty($used_company_ids)) {
                        $companies = get_terms([
                            'taxonomy' => 'company',
                            'include' => array_unique($used_company_ids),
                            'orderby' => 'name',
                            'order' => 'ASC'
                        ]);
                    }
                    
                    if ($companies && !is_wp_error($companies)) : ?>
                        <div class="filter-group">
                            <label for="company-filter"><i class="fas fa-building"></i> Company</label>
                            <select id="company-filter">
                                <option value="">All Companies</option>
                                <?php foreach ($companies as $company) : ?>
                                    <option value="<?php echo esc_attr($company->slug); ?>">
                                        <?php echo esc_html($company->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Work Grid Section -->
    <section class="work-grid">
        <div class="container">
            <div class="work-list">
                <?php
                // Combine all projects and deliverables
                $all_work = array_merge($all_projects, $all_deliverables);
                
                // Sort by date (newest first)
                usort($all_work, function($a, $b) {
                    return strtotime($b->post_date) - strtotime($a->post_date);
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
        </div>
    </section>

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contentTypeFilter = document.getElementById('content-type-filter'); // Now filters deliverable types
    const formatFilter = document.getElementById('format-filter'); // New filter for projects vs deliverables
    const projectFilter = document.getElementById('project-filter');
    const technologyFilter = document.getElementById('technology-filter');
    const companyFilter = document.getElementById('company-filter');
    const workCards = document.querySelectorAll('.card--project, .card--deliverable');
    
    // Filter toggle functionality
    const filterToggle = document.getElementById('filter-toggle');
    const filterForm = document.getElementById('filter-form');
    const filterToggleIcon = filterToggle.querySelector('.filter-toggle-icon');
    const resetButtonInline = document.getElementById('reset-filters-inline');
    
    function toggleFilters() {
        const isExpanded = filterToggle.getAttribute('aria-expanded') === 'true';
        
        if (isExpanded) {
            // Close filters with animation
            filterForm.classList.remove('filter-form--visible');
            setTimeout(() => {
                filterForm.style.display = 'none';
            }, 300); // Match CSS transition duration
            
            filterToggle.setAttribute('aria-expanded', 'false');
            filterForm.setAttribute('aria-hidden', 'true');
        } else {
            // Open filters with animation
            filterForm.style.display = 'block';
            // Force reflow to ensure display:block is applied before adding class
            filterForm.offsetHeight;
            filterForm.classList.add('filter-form--visible');
            
            filterToggle.setAttribute('aria-expanded', 'true');
            filterForm.setAttribute('aria-hidden', 'false');
        }
    }
    
    // Count active filters for better UX
    function countActiveFilters() {
        let count = 0;
        if (contentTypeFilter && contentTypeFilter.value !== '') count++;
        if (formatFilter && formatFilter.value !== '') count++;
        if (projectFilter && projectFilter.value !== '') count++;
        if (technologyFilter && technologyFilter.value !== '') count++;
        if (companyFilter && companyFilter.value !== '') count++;
        return count;
    }
    

    
    // Add click event to filter toggle
    if (filterToggle) {
        filterToggle.addEventListener('click', toggleFilters);
    }
    
    // Initialize filters as closed
    if (filterForm) {
        filterForm.style.display = 'none';
    }
    
    // Ensure all cards are visible on page load
    workCards.forEach(function(card) {
        card.style.display = '';
    });
    
    function filterWork() {
        const selectedContentType = contentTypeFilter ? contentTypeFilter.value : ''; // Deliverable types
        const selectedFormat = formatFilter ? formatFilter.value : ''; // Projects vs deliverables
        const selectedProject = projectFilter ? projectFilter.value : '';
        const selectedTechnology = technologyFilter ? technologyFilter.value : '';
        const selectedCompany = companyFilter ? companyFilter.value : '';
        
        let visibleCount = 0;
        
        workCards.forEach(function(card) {
            let showCard = true;
            
            // Filter by content type (deliverable types like videos, wireframes, etc.)
            if (selectedContentType !== '') {
                const cardContentTypesAttr = card.getAttribute('data-content-type') || '';
                const cardContentTypes = cardContentTypesAttr ? cardContentTypesAttr.split(',') : [];
                if (!cardContentTypes.includes(selectedContentType)) {
                    showCard = false;
                }
            }
            
            // Filter by format (projects vs deliverables)
            if (selectedFormat !== '' && showCard) {
                const cardFormat = card.getAttribute('data-format') || '';
                if (cardFormat !== selectedFormat) {
                    showCard = false;
                }
            }
            
            // Filter by project (applies to both projects and deliverables)
            if (selectedProject !== '' && showCard) {
                const cardProjectsAttr = card.getAttribute('data-projects') || '';
                const cardProjects = cardProjectsAttr ? cardProjectsAttr.split(',') : [];
                if (!cardProjects.includes(selectedProject)) {
                    showCard = false;
                }
            }
            
            // Filter by tool
            if (selectedTechnology !== '' && showCard) {
                const cardTechnologiesAttr = card.getAttribute('data-technologies') || '';
                const cardTechnologies = cardTechnologiesAttr ? cardTechnologiesAttr.split(',') : [];
                if (!cardTechnologies.includes(selectedTechnology)) {
                    showCard = false;
                }
            }
            
            // Filter by company
            if (selectedCompany !== '' && showCard) {
                const cardCompaniesAttr = card.getAttribute('data-companies') || '';
                const cardCompanies = cardCompaniesAttr ? cardCompaniesAttr.split(',') : [];
                if (!cardCompanies.includes(selectedCompany)) {
                    showCard = false;
                }
            }
            
            // Show/hide card
            if (showCard) {
                card.style.display = '';  // Use empty string to restore default display
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Show/hide no results message
        const noResultsMessage = document.querySelector('.no-work-found');
        const workList = document.querySelector('.work-list');
        
        if (visibleCount === 0) {
            if (!noResultsMessage) {
                const message = document.createElement('p');
                message.className = 'no-work-found';
                message.textContent = 'No work found matching your criteria.';
                workList.parentNode.appendChild(message);
            }
        } else {
            if (noResultsMessage) {
                noResultsMessage.remove();
            }
        }
    }
    
    // Check if any filters are active
    function checkFiltersActive() {
        const hasActiveFilters = (contentTypeFilter && contentTypeFilter.value !== '') ||
                                (formatFilter && formatFilter.value !== '') ||
                                (projectFilter && projectFilter.value !== '') ||
                                (technologyFilter && technologyFilter.value !== '') ||
                                (companyFilter && companyFilter.value !== '');
        
        // Update inline reset button visibility
        if (resetButtonInline) {
            if (hasActiveFilters) {
                resetButtonInline.classList.add('visible');
            } else {
                resetButtonInline.classList.remove('visible');
            }
        }
        
        // Add visual indicator to filter toggle when filters are active
        if (filterToggle) {
            if (hasActiveFilters) {
                filterToggle.classList.add('has-active-filters');
            } else {
                filterToggle.classList.remove('has-active-filters');
            }
        }
    }
    
    // Reset filters function
    function resetFilters() {
        if (contentTypeFilter) contentTypeFilter.value = '';
        if (formatFilter) formatFilter.value = '';
        if (projectFilter) projectFilter.value = '';
        if (technologyFilter) technologyFilter.value = '';
        if (companyFilter) companyFilter.value = '';
        
        // Show all cards
        workCards.forEach(function(card) {
            card.style.display = '';  // Use empty string to restore default display
        });
        
        // Remove any no results message
        const noResultsMessage = document.querySelector('.no-work-found');
        if (noResultsMessage) {
            noResultsMessage.remove();
        }
        
        // Update button states
        checkFiltersActive();
    }
    
    // Add event listeners
    const filters = [contentTypeFilter, formatFilter, projectFilter, technologyFilter, companyFilter];
    filters.forEach(filter => {
        if (filter) {
            filter.addEventListener('change', function() {
                filterWork();
                checkFiltersActive();
            });
        }
    });
    
    // Reset button event listener (inline button)
    if (resetButtonInline) {
        resetButtonInline.addEventListener('click', resetFilters);
        checkFiltersActive();
    }
});
</script>

<?php get_footer(); ?> 