<?php
/**
 * Standalone Company Data Recovery Script
 * 
 * This script recovers company URLs and logos that may have been lost
 * during ACF field structure changes.
 * 
 * Usage:
 * 1. Upload this file to your theme directory
 * 2. Go to: yoursite.com/wp-content/themes/ethanede/recover-company-data.php
 * 3. Or use the admin interface at Tools > Taxonomy Migration
 */

// Load WordPress
$wp_load_paths = [
    '../../../wp-load.php',
    '../../../../wp-load.php',
    '../../../../../wp-load.php'
];

foreach ($wp_load_paths as $path) {
    if (file_exists($path)) {
        require_once($path);
        break;
    }
}

if (!defined('ABSPATH')) {
    die('WordPress not found. Please ensure this file is in the correct theme directory.');
}

// Check if user is admin (when accessed directly)
if (!is_admin() && !current_user_can('manage_options')) {
    wp_die('You do not have sufficient permissions to access this page.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Company Data Recovery</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; padding: 20px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .button { background: #0073aa; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .button:hover { background: #005a85; }
        ul { margin-left: 20px; }
        .company-result { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 4px; }
        .company-name { font-weight: bold; color: #333; margin-bottom: 10px; }
        .update-item { margin-bottom: 5px; }
    </style>
</head>
<body>
    <h1>🔄 Company Data Recovery Tool</h1>
    
    <?php
    // Include the company info array from migrate-taxonomies.php
    require_once(get_template_directory() . '/migrate-taxonomies.php');
    
    if (isset($_POST['run_recovery'])) {
        echo '<div class="info"><strong>Running data recovery...</strong></div>';
        
        global $company_info;
        
        // Get all company terms
        $companies = get_terms([
            'taxonomy' => 'company',
            'hide_empty' => false,
        ]);
        
        if (empty($companies) || is_wp_error($companies)) {
            echo '<div class="info">No company terms found.</div>';
        } else {
            $recovery_log = [];
            $total_companies = count($companies);
            $companies_updated = 0;
            
            foreach ($companies as $company) {
                $company_name = $company->name;
                $company_id = $company->term_id;
                $updates_made = [];
                
                // 1. Recover website URL
                $current_acf_website = get_field('company_website', 'company_' . $company_id);
                if (empty($current_acf_website)) {
                    // Check term meta first
                    $meta_website = get_term_meta($company_id, 'website', true);
                    if (!empty($meta_website)) {
                        update_field('company_website', $meta_website, 'company_' . $company_id);
                        $updates_made[] = "✅ Website URL restored from term meta: " . $meta_website;
                    } else {
                        // Fallback to predefined company info
                        if (isset($company_info[$company_name]['website']) && !empty($company_info[$company_name]['website'])) {
                            update_field('company_website', $company_info[$company_name]['website'], 'company_' . $company_id);
                            $updates_made[] = "✅ Website URL set from company database: " . $company_info[$company_name]['website'];
                        }
                    }
                } else {
                    $updates_made[] = "ℹ️ Website URL already set: " . $current_acf_website;
                }
                
                // 2. Auto-link company logo
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
                            $file_path = get_attached_file($attachment_id);
                            $file_name = basename($file_path);
                            $updates_made[] = "✅ Logo linked from media library: " . $file_name;
                            $logo_found = true;
                            break;
                        }
                    }
                    
                    // Alternative search by filename in title
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
                                $updates_made[] = "✅ Logo linked from media library (title search): " . $attachments[0]->post_title;
                                $logo_found = true;
                                break;
                            }
                        }
                    }
                    
                    if (!$logo_found) {
                        $updates_made[] = "❌ No logo found in media library. Searched for: " . implode(', ', array_slice($search_patterns, 0, 3));
                    }
                } else {
                    $logo_url = is_array($current_acf_logo) ? $current_acf_logo['url'] : $current_acf_logo;
                    $updates_made[] = "ℹ️ Logo already set: " . basename($logo_url);
                }
                
                if (!empty($updates_made)) {
                    $recovery_log[$company_name] = $updates_made;
                    $companies_updated++;
                }
            }
            
            // Display results
            echo '<div class="success">';
            echo '<h3>🎉 Recovery Complete!</h3>';
            echo '<p><strong>Summary:</strong> ' . $companies_updated . ' out of ' . $total_companies . ' companies had data updates.</p>';
            echo '</div>';
            
            if (!empty($recovery_log)) {
                echo '<h3>Detailed Results:</h3>';
                foreach ($recovery_log as $company_name => $updates) {
                    echo '<div class="company-result">';
                    echo '<div class="company-name">' . esc_html($company_name) . '</div>';
                    foreach ($updates as $update) {
                        echo '<div class="update-item">' . esc_html($update) . '</div>';
                    }
                    echo '</div>';
                }
            }
        }
    } else {
        ?>
        <div class="info">
            <p><strong>This tool will help recover missing company data.</strong></p>
            <p>Specifically, it will:</p>
            <ul>
                <li>Restore website URLs from existing WordPress term meta or predefined company database</li>
                <li>Automatically link company logos from your Media Library based on filename matching</li>
                <li>Provide a detailed report of all changes made</li>
            </ul>
            <p><strong>This is safe to run multiple times</strong> - it will only update empty fields.</p>
        </div>
        
        <form method="post">
            <p>
                <input type="submit" name="run_recovery" value="🔄 Start Data Recovery" class="button" onclick="return confirm('Ready to recover company data? This will search for missing URLs and logos.');">
            </p>
        </form>
        <?php
    }
    ?>
    
    <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd;">
        <p><strong>Alternative Access:</strong> You can also run this via WordPress admin at <strong>Tools → Taxonomy Migration</strong></p>
        <p><a href="<?php echo admin_url('tools.php?page=taxonomy-migration'); ?>" class="button">Go to Admin Interface</a></p>
    </div>
</body>
</html> 