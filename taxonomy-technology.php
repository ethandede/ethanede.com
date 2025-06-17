<?php
/**
 * Template for displaying technology archive
 */

get_header();
?>

<!-- Background Animation Container -->
<div class="background-animation">
  <svg class="animated-squares" viewBox="0 0 1920 1080" preserveAspectRatio="xMidYMid slice"></svg>
</div>

<!-- Navigation -->
<?php get_template_part('partials/site-navigation'); ?>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu">
  <ul class="mobile-nav-links">
    <li><a href="<?php echo home_url(); ?>" class="text-semibold">Home</a></li>
    <li><a href="<?php echo home_url(); ?>#about" class="text-semibold">About</a></li>
    <li><a href="<?php echo home_url(); ?>#skills" class="text-semibold">Skills & Experience</a></li>
    <li><a href="<?php echo home_url(); ?>#contact" class="contact-trigger text-semibold">Contact</a></li>
  </ul>
</div>

<!-- Color controls UI -->
<?php get_template_part('partials/color-controls'); ?>

<!-- Get current technology term -->
<?php
$technology = get_queried_object();
$technology_id = $technology->term_id;
?>

<div id="technology-archive" class="technology-archive">
    <section class="archive-header">
        <div class="container">
            <h1><?php echo esc_html($technology->name); ?></h1>
            <?php if ($technology->description) : ?>
                <div class="supporting-text">
                    <?php echo wp_kses_post($technology->description); ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="archive-stats">
        <div class="container">
            <?php
            // Get all deliverables using this technology
            $deliverables = get_posts([
                'post_type' => 'deliverable',
                'posts_per_page' => -1,
                'tax_query' => [
                    [
                        'taxonomy' => 'technology',
                        'field' => 'term_id',
                        'terms' => $technology_id,
                    ],
                ],
            ]);

            // Get unique deliverable types and skills
            $deliverable_types = [];
            $skills = [];

            foreach ($deliverables as $deliverable) {
                // Get deliverable types
                $types = get_the_terms($deliverable->ID, 'deliverable_type');
                if ($types && !is_wp_error($types)) {
                    foreach ($types as $type) {
                        $deliverable_types[$type->term_id] = $type;
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
                    <span class="stat-label">Types</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?php echo count($skills); ?></span>
                    <span class="stat-label">Skills</span>
                </div>
            </div>
        </div>
    </section>

    <section class="deliverables-grid">
        <div class="container">
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

                <div class="deliverables-list">
                    <?php foreach ($deliverables as $deliverable) : 
                        $featured_media = get_field('featured_media', $deliverable->ID);
                        $types = get_the_terms($deliverable->ID, 'deliverable_type');
                        $type_classes = '';
                        if ($types && !is_wp_error($types)) {
                            $type_classes = implode(' ', wp_list_pluck($types, 'slug'));
                        }
                    ?>
                        <article class="deliverable-card <?php echo esc_attr($type_classes); ?>">
                            <?php if ($featured_media) : ?>
                                <div class="deliverable-card__image">
                                    <img src="<?php echo esc_url($featured_media); ?>" 
                                         alt="<?php echo esc_attr($deliverable->post_title); ?>">
                                </div>
                            <?php endif; ?>
                            
                            <div class="deliverable-card__content">
                                <h3><a href="<?php echo get_permalink($deliverable->ID); ?>">
                                    <?php echo esc_html($deliverable->post_title); ?>
                                </a></h3>
                                
                                <?php if ($types && !is_wp_error($types)) : ?>
                                    <div class="deliverable-card__type">
                                        <?php foreach ($types as $type) : ?>
                                            <span class="type"><?php echo esc_html($type->name); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p class="no-deliverables">No deliverables found using this technology.</p>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeFilter = document.getElementById('type-filter');
    const deliverables = document.querySelectorAll('.deliverable-card');
    
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