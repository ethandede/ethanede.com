<?php

if (!defined('ABSPATH')) {
    exit;
}

class ACF_Field_Excel_Embed extends acf_field {
    
    function __construct() {
        $this->name = 'excel_embed';
        $this->label = __('Excel/Google Sheets Embed', 'acf');
        $this->category = 'content';
        $this->defaults = [
            'sheet_url' => '',
            'sheet_name' => '',
            'enable_filters' => 1,
            'enable_search' => 1,
            'rows_per_page' => 20,
            'show_status_indicators' => 1,
            'custom_styling' => 1
        ];
        
        parent::__construct();
    }
    
    function render_field($field) {
        $value = $field['value'];
        if (!is_array($value)) {
            $value = [];
        }
        
        $value = wp_parse_args($value, $this->defaults);
        ?>
        
        <div class="acf-excel-embed-field">
            <div class="acf-excel-embed-row">
                <div class="acf-label">
                    <label for="<?php echo esc_attr($field['id']); ?>_url"><?php _e('Google Sheets URL', 'acf'); ?></label>
                </div>
                <div class="acf-input">
                    <input type="url" 
                           id="<?php echo esc_attr($field['id']); ?>_url" 
                           name="<?php echo esc_attr($field['name']); ?>[sheet_url]" 
                           value="<?php echo esc_attr($value['sheet_url']); ?>" 
                           placeholder="https://docs.google.com/spreadsheets/d/..."
                           class="excel-sheet-url" />
                    <p class="description"><?php _e('Enter the full Google Sheets URL (must be publicly accessible)', 'acf'); ?></p>
                </div>
            </div>
            
            <div class="acf-excel-embed-row">
                <div class="acf-label">
                    <label for="<?php echo esc_attr($field['id']); ?>_sheet"><?php _e('Sheet Name (optional)', 'acf'); ?></label>
                </div>
                <div class="acf-input">
                    <input type="text" 
                           id="<?php echo esc_attr($field['id']); ?>_sheet" 
                           name="<?php echo esc_attr($field['name']); ?>[sheet_name]" 
                           value="<?php echo esc_attr($value['sheet_name']); ?>" 
                           placeholder="Sheet1" 
                           class="excel-sheet-name" />
                    <p class="description"><?php _e('Leave blank to use the first sheet', 'acf'); ?></p>
                </div>
            </div>
            
            <div class="acf-excel-embed-row">
                <div class="acf-label">
                    <label><?php _e('Display Options', 'acf'); ?></label>
                </div>
                <div class="acf-input">
                    <ul class="acf-checkbox-list">
                        <li>
                            <label>
                                <input type="checkbox" 
                                       name="<?php echo esc_attr($field['name']); ?>[enable_filters]" 
                                       value="1" 
                                       <?php checked($value['enable_filters'], 1); ?> />
                                <?php _e('Enable column filters', 'acf'); ?>
                            </label>
                        </li>
                        <li>
                            <label>
                                <input type="checkbox" 
                                       name="<?php echo esc_attr($field['name']); ?>[enable_search]" 
                                       value="1" 
                                       <?php checked($value['enable_search'], 1); ?> />
                                <?php _e('Enable search functionality', 'acf'); ?>
                            </label>
                        </li>
                        <li>
                            <label>
                                <input type="checkbox" 
                                       name="<?php echo esc_attr($field['name']); ?>[show_status_indicators]" 
                                       value="1" 
                                       <?php checked($value['show_status_indicators'], 1); ?> />
                                <?php _e('Show status indicators (Done, Pending, etc.)', 'acf'); ?>
                            </label>
                        </li>
                        <li>
                            <label>
                                <input type="checkbox" 
                                       name="<?php echo esc_attr($field['name']); ?>[custom_styling]" 
                                       value="1" 
                                       <?php checked($value['custom_styling'], 1); ?> />
                                <?php _e('Apply theme styling', 'acf'); ?>
                            </label>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="acf-excel-embed-row">
                <div class="acf-label">
                    <label for="<?php echo esc_attr($field['id']); ?>_rows"><?php _e('Rows per page', 'acf'); ?></label>
                </div>
                <div class="acf-input">
                    <input type="number" 
                           id="<?php echo esc_attr($field['id']); ?>_rows" 
                           name="<?php echo esc_attr($field['name']); ?>[rows_per_page]" 
                           value="<?php echo esc_attr($value['rows_per_page']); ?>" 
                           min="5" 
                           max="100" 
                           class="excel-rows-per-page" />
                </div>
            </div>
            
            <div class="acf-excel-embed-preview">
                <button type="button" class="button button-secondary preview-excel-data">
                    <?php _e('Preview Data', 'acf'); ?>
                </button>
                <div class="excel-preview-container" style="display: none;">
                    <div class="excel-preview-loading"><?php _e('Loading preview...', 'acf'); ?></div>
                    <div class="excel-preview-content"></div>
                </div>
            </div>
        </div>
        
        <?php
    }
    
    function update_value($value, $post_id, $field) {
        if (!is_array($value)) {
            return $value;
        }
        
        // Sanitize the URL
        if (isset($value['sheet_url'])) {
            $value['sheet_url'] = esc_url_raw($value['sheet_url']);
        }
        
        // Sanitize sheet name
        if (isset($value['sheet_name'])) {
            $value['sheet_name'] = sanitize_text_field($value['sheet_name']);
        }
        
        // Sanitize boolean values
        $boolean_fields = ['enable_filters', 'enable_search', 'show_status_indicators', 'custom_styling'];
        foreach ($boolean_fields as $field_name) {
            if (isset($value[$field_name])) {
                $value[$field_name] = (bool) $value[$field_name];
            }
        }
        
        // Sanitize rows per page
        if (isset($value['rows_per_page'])) {
            $value['rows_per_page'] = absint($value['rows_per_page']);
            if ($value['rows_per_page'] < 5) $value['rows_per_page'] = 5;
            if ($value['rows_per_page'] > 100) $value['rows_per_page'] = 100;
        }
        
        return $value;
    }
    
    function format_value($value, $post_id, $field) {
        if (empty($value) || !is_array($value)) {
            return false;
        }
        
        return $value;
    }
    
    function render_field_settings($field) {
        // Additional field settings can be added here
        acf_render_field_setting($field, [
            'label' => __('Default Sheet URL', 'acf'),
            'instructions' => __('Enter a default Google Sheets URL', 'acf'),
            'name' => 'default_sheet_url',
            'type' => 'url'
        ]);
    }
}

// Field is initialized via functions.php to avoid duplicate registration 