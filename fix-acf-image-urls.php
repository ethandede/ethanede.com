<?php
/**
 * Fix ACF Image Field URLs
 * 
 * This script fixes ACF image field URLs that are pointing to deleted -scaled images
 * by updating them to point to the original images.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    require_once('wp-config.php');
    require_once('wp-load.php');
}

echo "🔧 Fixing ACF Image Field URLs...\n\n";

// Get all project categories
$categories = get_terms([
    'taxonomy' => 'project_category',
    'hide_empty' => false,
    'fields' => 'all'
]);

$fixed_count = 0;

foreach ($categories as $category) {
    $category_image = get_field('category_image', 'project_category_' . $category->term_id);
    
    if ($category_image && strpos($category_image, '-scaled') !== false) {
        echo "Found -scaled URL in category '{$category->name}': {$category_image}\n";
        
        // Try to find the original image
        $original_url = str_replace('-scaled', '', $category_image);
        
        // Check if original file exists
        $upload_dir = wp_upload_dir();
        $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $original_url);
        
        if (file_exists($file_path)) {
            // Update the ACF field
            update_field('category_image', $original_url, 'project_category_' . $category->term_id);
            echo "✅ Fixed: {$category->name} -> {$original_url}\n";
            $fixed_count++;
        } else {
            echo "❌ Original file not found: {$file_path}\n";
        }
    }
}

echo "\n🎉 Fixed {$fixed_count} ACF image field URLs!\n";

// Also check for any other ACF image fields that might have -scaled URLs
echo "\n🔍 Checking other ACF image fields...\n";

// Check company taxonomy
$companies = get_terms([
    'taxonomy' => 'company',
    'hide_empty' => false,
    'fields' => 'all'
]);

foreach ($companies as $company) {
    $company_logo = get_field('company_logo', 'company_' . $company->term_id);
    
    if ($company_logo && is_array($company_logo) && isset($company_logo['url'])) {
        $logo_url = $company_logo['url'];
        
        if (strpos($logo_url, '-scaled') !== false) {
            echo "Found -scaled URL in company '{$company->name}': {$logo_url}\n";
            
            // Try to find the original image
            $original_url = str_replace('-scaled', '', $logo_url);
            
            // Check if original file exists
            $upload_dir = wp_upload_dir();
            $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $original_url);
            
            if (file_exists($file_path)) {
                // Update the ACF field
                $company_logo['url'] = $original_url;
                update_field('company_logo', $company_logo, 'company_' . $company->term_id);
                echo "✅ Fixed: {$company->name} -> {$original_url}\n";
                $fixed_count++;
            } else {
                echo "❌ Original file not found: {$file_path}\n";
            }
        }
    }
}

echo "\n🎉 Total fixed: {$fixed_count} ACF image field URLs!\n";
echo "✨ Your homepage should now display correctly!\n"; 