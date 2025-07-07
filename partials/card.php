<?php
/**
 * Master Card Template Partial
 * 
 * Unified card component based on homepage portfolio cards as gold standard
 * Supports both projects and deliverables with context-specific modifications
 * 
 * @param array $args {
 *     Card configuration array
 *     
 *     @type string $type          Required. Card type: 'project' or 'deliverable'
 *     @type string $context       Optional. Context: 'archive', 'single', 'work', 'home'. Default 'archive'
 *     @type int    $post_id       Required. Post ID for content
 *     @type string $link_url      Optional. Custom link URL. Default: get_permalink($post_id)
 *     @type string $image_url     Optional. Custom image URL. Default: featured image
 *     @type string $image_alt     Optional. Custom image alt text. Default: post title
 *     @type array  $tags          Optional. Array of tag strings. Default: post taxonomies
 *     @type string $title         Optional. Custom title. Default: post title
 *     @type string $description   Optional. Custom description. Default: post excerpt
 *     @type bool   $show_media_types Optional. Show media type icons. Default false
 *     @type array  $media_types   Optional. Array of media types for icons

 *     @type array  $extra_classes Optional. Additional CSS classes
 * }
 */

// Validate required parameters
if (empty($args) || !is_array($args)) {
    return;
}

if (empty($args['type']) || !in_array($args['type'], ['project', 'deliverable'])) {
    return;
}

if (!isset($args['post_id']) || !is_numeric($args['post_id'])) {
    return;
}

// Allow post_id of 0 for taxonomy-only cards (like homepage)
$is_taxonomy_card = ($args['post_id'] === 0);

// Store original args to check what was explicitly set
$original_args = $args;

// Set defaults - handle taxonomy cards (post_id = 0) differently
$defaults = [
    'context' => 'archive',
    'link_url' => $is_taxonomy_card ? '#' : get_permalink($args['post_id']),
    'image_url' => $is_taxonomy_card ? '' : get_the_post_thumbnail_url($args['post_id'], 'card-thumbnail-small'),
    'image_alt' => $is_taxonomy_card ? '' : get_the_title($args['post_id']),
    'tags' => [],
    'title' => $is_taxonomy_card ? '' : get_the_title($args['post_id']),
    'description' => $is_taxonomy_card ? '' : get_the_excerpt($args['post_id']),
    'show_media_types' => false,
    'media_types' => [],

    'extra_classes' => []
];

$args = wp_parse_args($args, $defaults);

// Build CSS classes
$card_classes = ['card'];
$card_classes[] = 'card--' . esc_attr($args['type']);
$card_classes[] = 'card--' . esc_attr($args['context']);

if (!empty($args['extra_classes']) && is_array($args['extra_classes'])) {
    $card_classes = array_merge($card_classes, array_map('esc_attr', $args['extra_classes']));
}

// Get tags if not provided (skip for taxonomy cards)
// Only auto-populate if tags weren't explicitly set in original args
if (!array_key_exists('tags', $original_args) && !$is_taxonomy_card) {
    $taxonomy = ($args['type'] === 'project') ? 'project_category' : 'technology';
    $terms = get_the_terms($args['post_id'], $taxonomy);
    if ($terms && !is_wp_error($terms)) {
        $args['tags'] = array_map(function ($term) {
            return $term->name;
        }, $terms);
    }
}

// Get media types if enabled and not provided (skip for taxonomy cards)
if ($args['show_media_types'] && empty($args['media_types']) && !$is_taxonomy_card) {
    // Get media types from ACF fields or post meta
    $gallery_images = get_field('gallery_images', $args['post_id']);
    $gallery_videos = get_field('gallery_videos', $args['post_id']);
    $pdf_files = get_field('pdf_files', $args['post_id']);

    if (!empty($gallery_images)) {
        $args['media_types'][] = 'images';
    }
    if (!empty($gallery_videos)) {
        $args['media_types'][] = 'videos';
    }
    if (!empty($pdf_files)) {
        $args['media_types'][] = 'pdf';
    }
}

// Generate unique ID for accessibility
$card_id = 'card-' . $args['post_id'];

// Build data attributes string
$data_attributes_html = '';
if (!empty($args['data_attributes']) && is_array($args['data_attributes'])) {
    foreach ($args['data_attributes'] as $attr_name => $attr_value) {
        $data_attributes_html .= ' ' . esc_attr($attr_name) . '="' . esc_attr($attr_value) . '"';
    }
}
?>

<a href="<?php echo esc_url($args['link_url']); ?>" class="<?php echo esc_attr(implode(' ', $card_classes)); ?>"
    id="<?php echo esc_attr($card_id); ?>" aria-labelledby="<?php echo esc_attr($card_id); ?>-title"
    aria-describedby="<?php echo esc_attr($card_id); ?>-desc" <?php echo $data_attributes_html; ?>>

    <!-- Card Overlay -->
    <div class="card__overlay" aria-hidden="true"></div>

    <!-- Card Tags -->
    <?php if (!empty($args['tags']) && is_array($args['tags'])): ?>
        <div class="card__tags" aria-label="Tags">
            <?php foreach ($args['tags'] as $tag):
                // Determine tag class based on card type and tag content
                $tag_classes = ['tag'];

                // Add style class (cards use homepage/card style)
                $tag_classes[] = 'tag-style-card';

                // Add color class based on card type and tag content
                if ($args['type'] === 'project') {
                    $tag_classes[] = 'tag-project';
                } elseif ($args['type'] === 'deliverable') {
                    $tag_classes[] = 'tag-deliverable';
                }

                // Additional semantic classes based on tag content
                if (in_array(strtolower($tag), ['project'])) {
                    $tag_classes[] = 'tag-project-category';
                } elseif (in_array(strtolower($tag), ['animation', 'microsite', 'website', 'email'])) {
                    $tag_classes[] = 'tag-deliverable-type';
                }
                ?>
                <span class="<?php echo esc_attr(implode(' ', $tag_classes)); ?>">
                    <?php echo esc_html($tag); ?>
                </span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Card Image -->
    <?php if (!empty($args['image_url'])): ?>
        <div class="card__image-container">
            <img src="<?php echo esc_url($args['image_url']); ?>" 
                 srcset="<?php echo esc_url($args['image_url']); ?> 1x, <?php echo esc_url(get_the_post_thumbnail_url($args['post_id'], 'card-thumbnail')); ?> 2x"
                 alt="<?php echo esc_attr($args['image_alt']); ?>"
                class="card__image" loading="lazy">
        </div>
    <?php else: ?>
        <div class="card__image-container card__placeholder">
            <i class="fas fa-image" aria-hidden="true"></i>
        </div>
    <?php endif; ?>

    <!-- Card Content -->
    <div class="card__content">
        <div class="card__content-main">
            <h3 id="<?php echo esc_attr($card_id); ?>-title" class="card__title">
                <?php echo esc_html($args['title']); ?>
            </h3>

            <?php if (!empty($args['description'])): ?>
                <p id="<?php echo esc_attr($card_id); ?>-desc" class="card__description">
                    <?php echo esc_html($args['description']); ?>
                </p>
            <?php endif; ?>

            <!-- Media Types -->
            <?php if ($args['show_media_types'] && !empty($args['media_types'])): ?>
                <div class="card__media-types" aria-label="Available media types">
                    <?php foreach ($args['media_types'] as $media_type): ?>
                        <span class="media-type-icon" title="<?php echo esc_attr(ucfirst($media_type)); ?>">
                            <?php
                            $icon_class = 'fas fa-file';
                            switch ($media_type) {
                                case 'image':
                                    $icon_class = 'fas fa-images';
                                    break;
                                case 'video':
                                    $icon_class = 'fas fa-video';
                                    break;
                                case 'pdf':
                                    $icon_class = 'fas fa-file-pdf';
                                    break;
                            }
                            ?>
                            <i class="<?php echo esc_attr($icon_class); ?>" aria-hidden="true"></i>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="card__content-footer">
             <!-- Card Arrow (Always visible for navigation) -->
            <div class="card__arrow" aria-hidden="true">
                <i class="fas fa-arrow-right"></i>
            </div>
        </div>
    </div>
</a>