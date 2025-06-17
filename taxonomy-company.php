<?php
/**
 * Template for displaying company archive
 */

get_header();

// Get current company term
$company = get_queried_object();
$company_id = $company->term_id;

// Get company meta
$industry = get_term_meta($company_id, 'industry', true);
$website = get_term_meta($company_id, 'website', true);
$logo = get_field('company_logo', 'company_' . $company_id);
?>

<main id="company-archive" class="company-archive">
    <div class="container">
        <!-- Company Header -->
        <header class="company-header">
            <div class="company-info">
                <?php if ($logo) : ?>
                    <div class="company-logo">
                        <img src="<?php echo esc_url($logo['url']); ?>" 
                             alt="<?php echo esc_attr($logo['alt']); ?>"
                             width="<?php echo esc_attr($logo['width']); ?>"
                             height="<?php echo esc_attr($logo['height']); ?>">
                    </div>
                <?php endif; ?>
                
                <div class="company-details">
                    <h1><?php echo esc_html($company->name); ?></h1>
                    <?php if ($industry) : ?>
                        <div class="company-industry">
                            <span class="label">Industry:</span>
                            <span class="value"><?php echo esc_html($industry); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($website) : ?>
                        <div class="company-website">
                            <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener noreferrer">
                                Visit Website <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($company->description) : ?>
                <div class="company-description">
                    <?php echo wp_kses_post($company->description); ?>
                </div>
            <?php endif; ?>
        </header>

        <!-- Company Statistics -->
        <section class="company-stats">
            <?php
            // Get all deliverables for this company
            $deliverables = get_posts([
                'post_type' => 'deliverable',
                'posts_per_page' => -1,
                'tax_query' => [
                    [
                        'taxonomy' => 'company',
                        'field' => 'term_id',
                        'terms' => $company_id,
                    ],
                ],
            ]);

            // Get unique deliverable types
            $deliverable_types = [];
            $technologies = [];
            $skills = [];

            foreach ($deliverables as $deliverable) {
                // Get deliverable types
                $types = get_the_terms($deliverable->ID, 'deliverable_type');
                if ($types && !is_wp_error($types)) {
                    foreach ($types as $type) {
                        $deliverable_types[$type->term_id] = $type;
                    }
                }

                // Get technologies
                $techs = get_the_terms($deliverable->ID, 'technology');
                if ($techs && !is_wp_error($techs)) {
                    foreach ($techs as $tech) {
                        $technologies[$tech->term_id] = $tech;
                    }
                }

                // Get skills
                $delivery_skills = get_the_terms($deliverable->ID, 'skill');
                if ($delivery_skills && !is_wp_error($delivery_skills)) {
                    foreach ($delivery_skills as $skill) {
                        $skills[$skill->term_id] = $skill;
                    }
                }
            }
            ?>

            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-value"><?php echo count($deliverables); ?></span>
                    <span class="stat-label">Deliverables</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?php echo count($deliverable_types); ?></span>
                    <span class="stat-label">Types of Work</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?php echo count($technologies); ?></span>
                    <span class="stat-label">Technologies</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?php echo count($skills); ?></span>
                    <span class="stat-label">Skills Applied</span>
                </div>
            </div>
        </section>

        <!-- Deliverables Grid -->
        <section class="deliverables-grid">
            <h2>Deliverables</h2>
            
            <?php if (!empty($deliverables)) : ?>
                <div class="filter-controls">
                    <div class="filter-group">
                        <label for="type-filter">Filter by Type:</label>
                        <select id="type-filter">
                            <option value="">All Types</option>
                            <?php foreach ($deliverable_types as $type) : ?>
                                <option value="<?php echo esc_attr($type->slug); ?>">
                                    <?php echo esc_html($type->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid">
                    <?php foreach ($deliverables as $deliverable) : 
                        $featured_media = get_field('featured_media', $deliverable->ID);
                        $types = get_the_terms($deliverable->ID, 'deliverable_type');
                        $type_classes = '';
                        if ($types && !is_wp_error($types)) {
                            $type_classes = implode(' ', wp_list_pluck($types, 'slug'));
                        }
                    ?>
                        <article class="deliverable-item <?php echo esc_attr($type_classes); ?>">
                            <?php if ($featured_media) : ?>
                                <div class="deliverable-media">
                                    <img src="<?php echo esc_url($featured_media); ?>" 
                                         alt="<?php echo esc_attr($deliverable->post_title); ?>">
                                </div>
                            <?php endif; ?>
                            
                            <div class="deliverable-content">
                                <h3><?php echo esc_html($deliverable->post_title); ?></h3>
                                
                                <?php if ($types && !is_wp_error($types)) : ?>
                                    <div class="deliverable-types">
                                        <?php foreach ($types as $type) : ?>
                                            <span class="type-tag"><?php echo esc_html($type->name); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="deliverable-excerpt">
                                    <?php echo wp_trim_words($deliverable->post_content, 20); ?>
                                </div>
                                
                                <a href="<?php echo get_permalink($deliverable->ID); ?>" class="read-more">
                                    View Details <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p class="no-deliverables">No deliverables found for this company.</p>
            <?php endif; ?>
        </section>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeFilter = document.getElementById('type-filter');
    const deliverables = document.querySelectorAll('.deliverable-item');
    
    typeFilter.addEventListener('change', function() {
        const selectedType = this.value;
        
        deliverables.forEach(deliverable => {
            if (!selectedType || deliverable.classList.contains(selectedType)) {
                deliverable.style.display = 'block';
            } else {
                deliverable.style.display = 'none';
            }
        });
    });
});
</script>

<?php get_footer(); ?> 