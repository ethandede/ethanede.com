<?php
/**
 * Deliverable Query Helper
 * 
 * Provides reusable functions for querying deliverables and projects
 * with intelligent matching based on post titles and taxonomy terms
 */

class Deliverable_Query_Helper {
    
    /**
     * Get related items (projects and deliverables) for a post
     * 
     * @param string $post_title The title of the current post
     * @param array $options Query options
     * @return array Combined array of projects and deliverables
     */
    public static function get_related_items_for_post($post_title, $options = []) {
        $defaults = [
            'include_projects' => true,
            'include_deliverables' => true,
            'match_deliverable_types' => true,
            'fuzzy_match' => true
        ];
        
        $options = wp_parse_args($options, $defaults);
        $related_items = [];
        $deliverable_ids = [];
        
        // Step 1: Get projects by matching project_category
        if ($options['include_projects']) {
            $projects = self::get_matching_projects($post_title, $options['fuzzy_match']);
            
            foreach ($projects as $project_id) {
                $related_items[] = [
                    'type' => 'project',
                    'id' => $project_id
                ];
                
                // Get deliverables attached to this project
                if ($options['include_deliverables']) {
                    $project_deliverables = get_field('project_deliverables', $project_id);
                    if ($project_deliverables && is_array($project_deliverables)) {
                        foreach ($project_deliverables as $deliverable) {
                            $deliverable_id = is_object($deliverable) ? $deliverable->ID : $deliverable;
                            if (!in_array($deliverable_id, $deliverable_ids)) {
                                $related_items[] = [
                                    'type' => 'deliverable',
                                    'id' => $deliverable_id
                                ];
                                $deliverable_ids[] = $deliverable_id;
                            }
                        }
                    }
                }
            }
        }
        
        // Step 2: Get deliverables by matching deliverable_type
        if ($options['include_deliverables'] && $options['match_deliverable_types']) {
            $direct_deliverables = self::get_matching_deliverables($post_title, $options['fuzzy_match']);
            
            foreach ($direct_deliverables as $deliverable_id) {
                if (!in_array($deliverable_id, $deliverable_ids)) {
                    $related_items[] = [
                        'type' => 'deliverable',
                        'id' => $deliverable_id
                    ];
                    $deliverable_ids[] = $deliverable_id;
                }
            }
        }
        
        return $related_items;
    }
    
    /**
     * Get projects that match the given title in project_category taxonomy
     * 
     * @param string $title The title to match
     * @param bool $fuzzy_match Whether to use fuzzy matching
     * @return array Array of project IDs
     */
    private static function get_matching_projects($title, $fuzzy_match = true) {
        $query_terms = self::generate_term_variations($title, $fuzzy_match);
        
        $args = [
            'post_type' => 'project',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'tax_query' => [
                [
                    'taxonomy' => 'project_category',
                    'field' => 'name',
                    'terms' => $query_terms,
                    'operator' => 'IN',
                ],
            ],
        ];
        
        $query = new WP_Query($args);
        return $query->posts;
    }
    
    /**
     * Get deliverables that match the given title in deliverable_type taxonomy
     * 
     * @param string $title The title to match
     * @param bool $fuzzy_match Whether to use fuzzy matching
     * @return array Array of deliverable IDs
     */
    private static function get_matching_deliverables($title, $fuzzy_match = true) {
        // Get all deliverable_type terms that might match
        $matching_terms = self::find_matching_deliverable_types($title, $fuzzy_match);
        
        if (empty($matching_terms)) {
            return [];
        }
        
        $args = [
            'post_type' => 'deliverable',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'tax_query' => [
                [
                    'taxonomy' => 'deliverable_type',
                    'field' => 'slug',
                    'terms' => $matching_terms,
                    'operator' => 'IN',
                ],
            ],
        ];
        
        $query = new WP_Query($args);
        return $query->posts;
    }
    
    /**
     * Find deliverable_type terms that match the given title
     * 
     * @param string $title The title to match
     * @param bool $fuzzy_match Whether to use fuzzy matching
     * @return array Array of matching term slugs
     */
    private static function find_matching_deliverable_types($title, $fuzzy_match = true) {
        $matching_slugs = [];
        
        // Get all deliverable_type terms
        $terms = get_terms([
            'taxonomy' => 'deliverable_type',
            'hide_empty' => false,
        ]);
        
        if (is_wp_error($terms)) {
            return [];
        }
        
        $title_lower = strtolower($title);
        $title_words = self::extract_keywords($title);
        
        foreach ($terms as $term) {
            $term_name_lower = strtolower($term->name);
            $term_words = self::extract_keywords($term->name);
            
            // Exact match
            if ($term_name_lower === $title_lower) {
                $matching_slugs[] = $term->slug;
                continue;
            }
            
            if ($fuzzy_match) {
                // Check if any significant words from the title appear in the term name
                $word_match = false;
                foreach ($title_words as $word) {
                    if (strlen($word) > 3 && stripos($term_name_lower, $word) !== false) {
                        $word_match = true;
                        break;
                    }
                }
                
                // Check if any significant words from the term appear in the title
                if (!$word_match) {
                    foreach ($term_words as $word) {
                        if (strlen($word) > 3 && stripos($title_lower, $word) !== false) {
                            $word_match = true;
                            break;
                        }
                    }
                }
                
                if ($word_match) {
                    $matching_slugs[] = $term->slug;
                }
            }
        }
        
        return $matching_slugs;
    }
    
    /**
     * Generate variations of a term for matching
     * 
     * @param string $title The title to generate variations for
     * @param bool $fuzzy_match Whether to generate fuzzy variations
     * @return array Array of term variations
     */
    private static function generate_term_variations($title, $fuzzy_match = true) {
        $variations = [$title];
        
        if ($fuzzy_match) {
            // Add variation with & replaced by "and"
            if (strpos($title, '&') !== false) {
                $variations[] = str_replace('&', 'and', $title);
                $variations[] = str_replace(' & ', ' and ', $title);
            }
            
            // Add variation with "and" replaced by &
            if (stripos($title, ' and ') !== false) {
                $variations[] = str_ireplace(' and ', ' & ', $title);
                $variations[] = str_ireplace(' and ', '&', $title);
            }
            
            // Extract significant words for broader matching
            $keywords = self::extract_keywords($title);
            foreach ($keywords as $keyword) {
                if (strlen($keyword) > 3) {
                    $variations[] = $keyword;
                }
            }
        }
        
        return array_unique($variations);
    }
    
    /**
     * Extract significant keywords from a string
     * 
     * @param string $text The text to extract keywords from
     * @return array Array of keywords
     */
    private static function extract_keywords($text) {
        // Remove common stop words and punctuation
        $stop_words = ['the', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'from', 'as', 'is', 'was', 'are', 'were'];
        
        // Convert to lowercase and split into words
        $text = strtolower($text);
        $text = preg_replace('/[^\w\s]/', ' ', $text);
        $words = explode(' ', $text);
        
        // Filter out stop words and empty strings
        $keywords = array_filter($words, function($word) use ($stop_words) {
            return !empty($word) && !in_array($word, $stop_words);
        });
        
        return array_values($keywords);
    }
    
    /**
     * Check if a post should only show projects (e.g., Website Management)
     * 
     * @param int $post_id The post ID to check
     * @return bool Whether to show only projects
     */
    public static function should_show_only_projects($post_id) {
        $categories = get_the_terms($post_id, 'project_category');
        
        if ($categories && !is_wp_error($categories)) {
            foreach ($categories as $cat) {
                if (strtolower($cat->name) === 'website management' || 
                    $cat->slug === 'website-management') {
                    return true;
                }
            }
        }
        
        return false;
    }
}