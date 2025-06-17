<?php get_header(); ?>

<main id="deliverables-archive">
    <section class="deliverables-header">
        <div class="container">
            <h1>Deliverables</h1>
            
            <!-- Filter Controls -->
            <div class="deliverables-filters">
                <form action="<?php echo esc_url(get_post_type_archive_link('deliverable')); ?>" method="get" class="filter-form">
                    <?php
                    // Project Filter
                    $projects = get_posts([
                        'post_type' => 'project',
                        'posts_per_page' => -1,
                        'orderby' => 'title',
                        'order' => 'ASC'
                    ]);
                    if ($projects) : ?>
                        <div class="filter-group">
                            <label for="project">Project</label>
                            <select name="project" id="project">
                                <option value="">All Projects</option>
                                <?php foreach ($projects as $project) : ?>
                                    <option value="<?php echo esc_attr($project->ID); ?>" 
                                            <?php selected(get_query_var('project'), $project->ID); ?>>
                                        <?php echo esc_html($project->post_title); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php
                    // Technology Filter
                    $technologies = get_terms([
                        'taxonomy' => 'technology',
                        'hide_empty' => true
                    ]);
                    if ($technologies && !is_wp_error($technologies)) : ?>
                        <div class="filter-group">
                            <label for="technology">Technology</label>
                            <select name="technology" id="technology">
                                <option value="">All Technologies</option>
                                <?php foreach ($technologies as $tech) : ?>
                                    <option value="<?php echo esc_attr($tech->slug); ?>"
                                            <?php selected(get_query_var('technology'), $tech->slug); ?>>
                                        <?php echo esc_html($tech->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php
                    // Skill Filter
                    $skills = get_terms([
                        'taxonomy' => 'skill',
                        'hide_empty' => true
                    ]);
                    if ($skills && !is_wp_error($skills)) : ?>
                        <div class="filter-group">
                            <label for="skill">Skill</label>
                            <select name="skill" id="skill">
                                <option value="">All Skills</option>
                                <?php foreach ($skills as $skill) : ?>
                                    <option value="<?php echo esc_attr($skill->slug); ?>"
                                            <?php selected(get_query_var('skill'), $skill->slug); ?>>
                                        <?php echo esc_html($skill->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="filter-submit">Apply Filters</button>
                </form>
            </div>
        </div>
    </section>

    <section class="deliverables-grid">
        <div class="container">
            <?php if (have_posts()) : ?>
                <div class="deliverables-list">
                    <?php while (have_posts()) : the_post(); ?>
                        <article class="deliverable-card">
                            <?php
                            // Get the first image from the gallery
                            $media = get_field('deliverable_media');
                            $first_image = null;
                            if ($media) {
                                foreach ($media as $item) {
                                    if ($item['type'] === 'image') {
                                        $first_image = $item;
                                        break;
                                    }
                                }
                            }
                            ?>
                            
                            <?php if ($first_image) : ?>
                                <div class="deliverable-card__image">
                                    <img src="<?php echo esc_url($first_image['sizes']['medium']); ?>" 
                                         alt="<?php echo esc_attr($first_image['alt']); ?>">
                                </div>
                            <?php endif; ?>

                            <div class="deliverable-card__content">
                                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                
                                <?php
                                // Get related project
                                $project_id = get_field('related_project');
                                if ($project_id) :
                                    $project = get_post($project_id);
                                    ?>
                                    <div class="deliverable-card__project">
                                        <span>Project:</span>
                                        <a href="<?php echo get_permalink($project_id); ?>">
                                            <?php echo esc_html($project->post_title); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php
                                // Get deliverable type
                                $type = get_field('deliverable_type');
                                if ($type) :
                                    $type_term = get_term($type, 'deliverable_type');
                                    ?>
                                    <div class="deliverable-card__type">
                                        <span>Type:</span>
                                        <?php echo esc_html($type_term->name); ?>
                                    </div>
                                <?php endif; ?>

                                <?php
                                // Get technologies and skills
                                $technologies = get_field('deliverable_technologies');
                                $skills = get_field('deliverable_skills');
                                if ($technologies || $skills) : ?>
                                    <div class="deliverable-card__tags">
                                        <?php 
                                        if ($technologies) :
                                            foreach ($technologies as $tech_id) :
                                                $tech = get_term($tech_id, 'technology');
                                                if ($tech && !is_wp_error($tech)) : ?>
                                                    <a href="<?php echo get_term_link($tech); ?>" class="tag tag-technology">
                                                        <?php echo esc_html($tech->name); ?>
                                                    </a>
                                                <?php endif;
                                            endforeach;
                                        endif;

                                        if ($skills) :
                                            foreach ($skills as $skill_id) :
                                                $skill = get_term($skill_id, 'skill');
                                                if ($skill && !is_wp_error($skill)) : ?>
                                                    <a href="<?php echo get_term_link($skill); ?>" class="tag tag-skill">
                                                        <?php echo esc_html($skill->name); ?>
                                                    </a>
                                                <?php endif;
                                            endforeach;
                                        endif;
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>

                <?php
                // Pagination
                the_posts_pagination([
                    'mid_size' => 2,
                    'prev_text' => __('Previous', 'ethanede'),
                    'next_text' => __('Next', 'ethanede'),
                ]);
                ?>

            <?php else : ?>
                <p class="no-deliverables">No deliverables found matching your criteria.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php get_footer(); ?> 