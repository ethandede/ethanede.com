<?php
// Debug information
error_log('Single Deliverable Template Loaded');
error_log('Post ID: ' . get_the_ID());
error_log('Post Type: ' . get_post_type());
error_log('Post Status: ' . get_post_status());

get_header(); ?>

<main id="deliverable">
    <?php while (have_posts()):
        the_post(); ?>
        <section class="deliverable-header">
            <div class="container">
                <?php ee_display_single_page_tag(null, ['wrapper_class' => 'deliverable-type-tag']); ?>
                
                <h1><?php the_title(); ?></h1>
                
                <?php
                // Show related project and company info below h1 on mobile
                ?>
                <div class="deliverable-meta-mobile">
                    <?php
                    // Related Project
                    $related_project = get_field('related_project');
                    if ($related_project && is_array($related_project) && !empty($related_project)) :
                        $project_id = $related_project[0];
                        $project = get_post($project_id);
                        if ($project && !is_wp_error($project)) :
                        ?>
                        <div class="meta-item-mobile">
                            <span class="meta-label">Related Project:</span>
                            <a href="<?php echo get_permalink($project_id); ?>" class="meta-value">
                                <?php echo esc_html($project->post_title); ?>
                            </a>
                        </div>
                    <?php 
                        endif;
                    endif; ?>

                    <?php
                    // Company info (While At)
                    $company = null;
                    $deliverable_companies = get_the_terms(get_the_ID(), 'company');
                    
                    if ($deliverable_companies && !is_wp_error($deliverable_companies)) {
                        $company = $deliverable_companies[0];
                    } else {
                        // Fallback: Get company from related project
                        if ($related_project && is_array($related_project) && !empty($related_project)) {
                            $project_id = $related_project[0];
                            $project_companies = get_the_terms($project_id, 'company');
                            if ($project_companies && !is_wp_error($project_companies)) {
                                $company = $project_companies[0];
                            }
                        }
                    }
                    
                    if ($company) :
                        $logo = get_field('company_logo', 'company_' . $company->term_id);
                        ?>
                                                 <div class="meta-item-mobile">
                             <span class="meta-label">While At:</span>
                             <a href="<?php echo esc_url(get_term_link($company)); ?>" class="meta-value company-link">
                                 <?php if ($logo && is_array($logo) && !empty($logo['url'])) : ?>
                                     <img src="<?php echo esc_url($logo['url']); ?>" alt="<?php echo esc_attr($company->name); ?> logo" class="company-logo-small">
                                 <?php else : ?>
                                     <?php echo esc_html($company->name); ?>
                                 <?php endif; ?>
                             </a>
                         </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="deliverable-content">
            <div class="container">
                <div class="deliverable-main">
                    <div class="deliverable-description">
                        <?php
                        // Clean and display the deliverable description
                        $description = get_field('deliverable_description');
                        if ($description) {
                            // Strip unwanted CSS classes and clean HTML
                            $clean_description = clean_deliverable_content($description);
                            echo $clean_description;
                        }
                        ?>
                    </div>

                    <?php
                    // Display deliverable media gallery (images and videos only)
                    $media = get_field('deliverable_media');
                    if ($media): ?>
                        <?php
                        // Get deliverable featured image to use as video poster if needed
                        $deliverable_featured = get_field('deliverable_featured_image');
                        ?>
                        <div class="deliverable-gallery">
                            <?php foreach ($media as $index => $item): ?>
                                <div class="gallery-item" data-index="<?php echo $index; ?>"
                                    data-type="<?php echo esc_attr($item['type']); ?>">
                                    <?php if ($item['type'] === 'image'): ?>
                                        <img src="<?php echo esc_url($item['url']); ?>" alt="<?php echo esc_attr($item['alt']); ?>"
                                            class="gallery-image">
                                    <?php elseif ($item['type'] === 'video'): ?>
                                        <?php if ($deliverable_featured): ?>
                                            <div class="video-poster-container">
                                                <img src="<?php echo esc_url($deliverable_featured); ?>" 
                                                     alt="Video thumbnail"
                                                     class="video-poster">
                                                <div class="video-play-overlay">
                                                    <div class="play-button">
                                                        <i class="fas fa-play"></i>
                                                    </div>
                                                </div>
                                                <video class="gallery-video" style="display: none;" 
                                                       data-src="<?php echo esc_url($item['url']); ?>"
                                                       data-type="<?php echo esc_attr($item['mime_type']); ?>">
                                                    <source src="<?php echo esc_url($item['url']); ?>"
                                                            type="<?php echo esc_attr($item['mime_type']); ?>">
                                                </video>
                                            </div>
                                        <?php else: ?>
                                            <video controls class="gallery-video">
                                                <source src="<?php echo esc_url($item['url']); ?>"
                                                        type="<?php echo esc_attr($item['mime_type']); ?>">
                                            </video>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Gallery Overlay for Images and Videos -->
                        <div id="gallery-overlay" class="gallery-overlay">
                            <div class="gallery-overlay-content">
                                <button class="gallery-close">&times;</button>
                                <button class="gallery-prev"><i class="fal fa-chevron-left"></i></button>
                                <button class="gallery-next"><i class="fal fa-chevron-right"></i></button>
                                <div class="gallery-carousel">
                                    <?php foreach ($media as $item): ?>
                                        <div class="carousel-item" data-type="<?php echo esc_attr($item['type']); ?>">
                                            <?php if ($item['type'] === 'image'): ?>
                                                <img src="<?php echo esc_url($item['url']); ?>"
                                                    alt="<?php echo esc_attr($item['alt']); ?>">
                                            <?php elseif ($item['type'] === 'video'): ?>
                                                <video controls <?php echo $deliverable_featured ? 'poster="' . esc_url($deliverable_featured) . '"' : ''; ?>>
                                                    <source src="<?php echo esc_url($item['url']); ?>"
                                                        type="<?php echo esc_attr($item['mime_type']); ?>">
                                                </video>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php
                    // Display PDF document section
                    $pdf_file = get_field('deliverable_pdf');
                    $pdf_cover = get_field('pdf_custom_cover');
                    $pdf_title = get_field('pdf_title');

                    if ($pdf_file): ?>
                        <div class="deliverable-pdf-section">
                            <div class="pdf-display">
                                <div class="pdf-icon-and-title">
                                                                            <div class="pdf-icon-and-meta">
                                        <div class="pdf-cover">
                                            <?php if ($pdf_cover): ?>
                                                <img src="<?php echo esc_url($pdf_cover['url']); ?>"
                                                    alt="<?php echo esc_attr($pdf_cover['alt'] ?: 'PDF Cover'); ?>"
                                                    class="pdf-custom-cover">
                                            <?php else: ?>
                                                <div class="pdf-default-cover">
                                                    <svg viewBox="20 30 140 180" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <!-- Document background -->
                                                        <rect x="20" y="30" width="140" height="180" rx="8" fill="#f8f9fa"
                                                            stroke="#343a40" stroke-width="2" />
                                                        <!-- Folded corner -->
                                                        <path d="M130 30 L160 60 L130 60 Z" fill="#dee2e6" stroke="#343a40"
                                                            stroke-width="2" />
                                                        <!-- PDF text -->
                                                        <text x="90" y="130" font-family="Arial, sans-serif" font-size="20"
                                                            font-weight="bold" text-anchor="middle" fill="#495057">PDF</text>
                                                        <!-- Document lines -->
                                                        <line x1="35" y1="150" x2="145" y2="150" stroke="#6c757d"
                                                            stroke-width="2" />
                                                        <line x1="35" y1="165" x2="125" y2="165" stroke="#6c757d"
                                                            stroke-width="2" />
                                                        <line x1="35" y1="180" x2="135" y2="180" stroke="#6c757d"
                                                            stroke-width="2" />
                                                    </svg>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <p class="pdf-meta">
                                            <span class="pdf-filesize"><?php echo size_format($pdf_file['filesize']); ?></span>
                                            <span class="pdf-separator">•</span>
                                            <span class="pdf-type">PDF</span>
                                        </p>
                                    </div>
                                    <?php if ($pdf_title): ?>
                                        <div class="pdf-title">
                                            <h4><?php echo esc_html($pdf_title); ?></h4>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="pdf-actions">
                                    <a href="<?php echo esc_url($pdf_file['url']); ?>" target="_blank" class="pdf-view-btn" rel="noopener noreferrer">
                                        View PDF
                                        <i class="fa-light fa-arrow-up-right-from-square"></i>
                                    </a>
                                    <a href="<?php echo esc_url($pdf_file['url']); ?>" download class="pdf-download-btn">
                                        Download
                                        <i class="fa-light fa-down-to-line"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php
                    // Display Excel/Spreadsheet embed
                    $excel_embed = get_field('deliverable_excel_embed');
                    if ($excel_embed && is_array($excel_embed) && !empty($excel_embed['sheet_url'])): ?>
                        <div class="deliverable-excel-section">
                            <?php 
                            // Get deliverable title for the Excel embed
                            $excel_title = get_the_title() . ' - Data';
                            
                            echo render_excel_embed($excel_embed, [
                                'title' => $excel_title,
                                'enable_search' => $excel_embed['enable_search'] ?? true,
                                'enable_filters' => $excel_embed['enable_filters'] ?? true,
                                'show_status_indicators' => $excel_embed['show_status_indicators'] ?? true,
                                'rows_per_page' => $excel_embed['rows_per_page'] ?? 5
                            ]); 
                            ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php
                // Include the flexible sidebar
                get_template_part('partials/project-sidebar', null, [
                    'context' => 'deliverable',
                    'config' => [
                        'show_meta' => true,
                        'show_tags' => true,
                        'show_related' => true,
                        'related_count' => 3,
                        'sidebar_class' => 'deliverable-layout-sidebar'
                    ]
                ]);
                ?>
            </div>
        </section>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>