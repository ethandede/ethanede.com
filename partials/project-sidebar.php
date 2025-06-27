<?php
/**
 * Flexible project/deliverable sidebar
 * Can be used by both single-project.php and single-deliverable.php
 * 
 * @param string $context - 'project' or 'deliverable'
 * @param array $config - Configuration options for the sidebar
 */

// Set defaults
$context = $args['context'] ?? 'project';
$config = $args['config'] ?? [];

// Default configuration
$default_config = [
    'show_meta' => true,
    'show_tags' => true,
    'show_related' => true,
    'related_count' => 3,
    'sidebar_class' => ''
];

$config = array_merge($default_config, $config);
$sidebar_class = $context === 'project' ? 'project-sidebar' : 'deliverable-sidebar';
$sidebar_class .= ' ' . $config['sidebar_class'];
?>

<aside class="<?php echo esc_attr(trim($sidebar_class)); ?>">
    <?php if ($config['show_meta']) : ?>
        <div class="meta-sidebar">
            <?php if ($context === 'deliverable') : ?>
                <?php
                // Get related project for deliverables
                $related_project = get_field('related_project');
                if ($related_project && is_array($related_project) && !empty($related_project)) :
                    $project_id = $related_project[0];
                    $project = get_post($project_id);
                    if ($project && !is_wp_error($project)) :
                    ?>
                    <div class="meta-item">
                        <h6>Related Project</h6>
                        <div class="meta-content">
                            <a href="<?php echo get_permalink($project_id); ?>" class="meta-link">
                                <?php echo esc_html($project->post_title); ?>
                            </a>
                        </div>
                    </div>
                <?php 
                    endif;
                endif; ?>

                <?php
                // Get deliverable type from taxonomy
                $type_terms = get_the_terms(get_the_ID(), 'deliverable_type');
                if ($type_terms && !is_wp_error($type_terms)) :
                    $type_term = $type_terms[0];
                    ?>
                    <div class="meta-item">
                        <h6>Deliverable Type</h6>
                        <div class="meta-content">
                            <span><?php echo esc_html(get_singular_term_display_name($type_term->name)); ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="meta-item">
                    <h6>While At</h6>
                    <div class="meta-content">
                        <?php 
                        // Get company directly from deliverable first, fallback to related project
                        $company = null;
                        $deliverable_companies = get_the_terms(get_the_ID(), 'company');
                        
                        if ($deliverable_companies && !is_wp_error($deliverable_companies)) {
                            $company = $deliverable_companies[0]; // Use direct company assignment
                        } else {
                            // Fallback: Get company from related project
                            $related_project = get_field('related_project');
                            if ($related_project && is_array($related_project) && !empty($related_project)) {
                                $project_id = $related_project[0];
                                $project_companies = get_the_terms($project_id, 'company');
                                if ($project_companies && !is_wp_error($project_companies)) {
                                    $company = $project_companies[0];
                                }
                            }
                        }
                        
                        if ($company) {
                            // Get company logo
                            $logo = get_field('company_logo', 'company_' . $company->term_id);
                            
                            echo '<a href="' . esc_url(get_term_link($company)) . '" class="company-name">';
                            if ($logo && is_array($logo) && !empty($logo['url'])) {
                                echo '<img src="' . esc_url($logo['url']) . '" alt="' . esc_attr($company->name) . ' logo" class="company-logo">';
                            } else {
                                echo esc_html($company->name);
                            }
                            echo '</a>';
                        }
                        ?>
                    </div>
                </div>

                <?php
                // Get deliverable status
                $status = get_field('deliverable_status');
                if ($status) :
                    $status_term = get_term($status, 'deliverable_status');
                    ?>
                    <div class="meta-item">
                        <h6>Status</h6>
                        <div class="meta-content">
                            <span><?php echo esc_html($status_term->name); ?></span>
                        </div>
                    </div>
                <?php endif; ?>

            <?php elseif ($context === 'project') : ?>
                <?php
                // Get project company
                $company_terms = get_the_terms(get_the_ID(), 'company');
                if ($company_terms && !is_wp_error($company_terms)) :
                    $company = $company_terms[0];
                ?>
                    <div class="meta-item">
                        <h6>While At</h6>
                        <div class="meta-content">
                            <?php
                            // Get company logo
                            $logo = get_field('company_logo', 'company_' . $company->term_id);
                            ?>
                            <span class="company-name">
                                <?php if ($logo && is_array($logo) && !empty($logo['url'])) : ?>
                                    <img src="<?php echo esc_url($logo['url']); ?>" alt="<?php echo esc_attr($company->name); ?> logo" class="company-logo">
                                <?php else : ?>
                                    <?php echo esc_html($company->name); ?>
                                    <?php if (current_user_can('manage_options')) : ?>
                                        <!-- Debug info for admins -->
                                        <small style="display: block; opacity: 0.5; font-size: 10px;">
                                            Debug: Company ID: <?php echo $company->term_id; ?>, Logo data: <?php echo $logo ? 'exists but invalid' : 'not found'; ?>
                                        </small>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                <?php endif; ?>

                <?php
                // Get project role
                $role = get_field('project_role');
                if ($role) : ?>
                    <div class="meta-item">
                        <h6>Role</h6>
                        <div class="meta-content">
                            <span><?php echo esc_html($role); ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <?php
                // Get project skills
                $skills = get_the_terms(get_the_ID(), 'skill');
                if ($skills && !is_wp_error($skills)) : 
                    // Sort skills by usage count (descending)
                    usort($skills, function($a, $b) {
                        return $b->count - $a->count;
                    });
                    ?>
                    <div class="tags-sidebar">
                        <h6>Skills Used</h6>
                        <div class="tags-cloud">
                            <?php foreach ($skills as $skill) : ?>
                                <span class="tag tag-style-sidebar tag-skill tag-non-clickable">
                                    <?php echo esc_html($skill->name); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php
                // Get project tools
                $tools = get_the_terms(get_the_ID(), 'technology');
                if ($tools && !is_wp_error($tools)) : 
                    // Sort tools by usage count (descending)
                    usort($tools, function($a, $b) {
                        return $b->count - $a->count;
                    });
                    ?>
                    <div class="tags-sidebar">
                        <h6>Tools Used</h6>
                        <div class="tags-cloud">
                            <?php foreach ($tools as $tool) : ?>
                                <span class="tag tag-style-sidebar tag-technology tag-non-clickable">
                                    <?php echo esc_html($tool->name); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php
                // Get project timeline/duration
                $start_date = get_field('project_start_date');
                $end_date = get_field('project_end_date');
                if ($start_date || $end_date) : ?>
                    <div class="meta-item">
                        <h6>Timeline</h6>
                        <div class="meta-content">
                            <?php if ($start_date) : ?>
                                <span class="start-date">Started: <?php echo date('M Y', strtotime($start_date)); ?></span>
                            <?php endif; ?>
                            <?php if ($end_date) : ?>
                                <span class="end-date">Completed: <?php echo date('M Y', strtotime($end_date)); ?></span>
                            <?php elseif ($start_date) : ?>
                                <span class="ongoing">Ongoing</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($config['show_tags']) : ?>
        <?php
        if ($context === 'deliverable') {
            // Get deliverable tools
            $tools = get_the_terms(get_the_ID(), 'technology');
            if ($tools && !is_wp_error($tools)) : 
                // Sort tools by usage count (descending)
                usort($tools, function($a, $b) {
                    return $b->count - $a->count;
                });
                ?>
                <div class="tags-sidebar">
                    <h6>Tools Used</h6>
                    <div class="tags-cloud">
                        <?php foreach ($tools as $tool) : ?>
                            <span class="tag tag-style-sidebar tag-technology tag-non-clickable">
                                <?php echo esc_html($tool->name); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif;

            // Get deliverable skills
            $skills = get_the_terms(get_the_ID(), 'skill');
            if ($skills && !is_wp_error($skills)) : 
                // Sort skills by usage count (descending)
                usort($skills, function($a, $b) {
                    return $b->count - $a->count;
                });
                ?>
                <div class="tags-sidebar">
                    <h6>Skills Used</h6>
                    <div class="tags-cloud">
                        <?php foreach ($skills as $skill) : ?>
                            <span class="tag tag-style-sidebar tag-skill tag-non-clickable">
                                <?php echo esc_html($skill->name); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif;

            // Get deliverable type - KEEP CLICKABLE since we have deliverable archives
            $types = get_the_terms(get_the_ID(), 'deliverable_type');
            if ($types && !is_wp_error($types)) : 
                // Sort types by usage count (descending)
                usort($types, function($a, $b) {
                    return $b->count - $a->count;
                });
                ?>
                <div class="tags-sidebar">
                    <h6>Type</h6>
                    <div class="tags-cloud">
                        <?php foreach ($types as $type) : ?>
                            <?php 
                            $display_name = $type->name;
                            // Convert plural forms to singular for display
                            if ($display_name === 'Microsites') {
                                $display_name = 'Microsite';
                            } elseif ($display_name === 'Animations') {
                                $display_name = 'Animation';
                            }
                            ?>
                            <span class="tag tag-style-sidebar tag-deliverable-type tag-non-clickable">
                                <?php echo esc_html($display_name); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif;

        } elseif ($context === 'project') {
            // Get project categories
            $categories = get_the_terms(get_the_ID(), 'project_category');
            if ($categories && !is_wp_error($categories)) : 
                // Sort categories by usage count (descending)
                usort($categories, function($a, $b) {
                    return $b->count - $a->count;
                });
                ?>
                <div class="tags-sidebar">
                    <h6>Category</h6>
                    <div class="tags-cloud">
                        <?php foreach ($categories as $category) : ?>
                            <span class="tag tag-style-sidebar tag-project-category tag-non-clickable">
                                <?php echo esc_html($category->name); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif;


        }
        ?>
    <?php endif; ?>

</aside> 