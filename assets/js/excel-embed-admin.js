jQuery(document).ready(function($) {
    
    // Handle preview button clicks
    $(document).on('click', '.preview-excel-data', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var $field = $button.closest('.acf-excel-embed-field');
        var $previewContainer = $field.find('.excel-preview-container');
        var $loading = $field.find('.excel-preview-loading');
        var $content = $field.find('.excel-preview-content');
        
        var sheetUrl = $field.find('.excel-sheet-url').val();
        var sheetName = $field.find('.excel-sheet-name').val();
        
        if (!sheetUrl) {
            alert('Please enter a Google Sheets URL first.');
            return;
        }
        
        // Show loading state
        $previewContainer.show();
        $loading.show();
        $content.empty();
        $button.prop('disabled', true).text('Loading...');
        
        // Fetch preview data via AJAX
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'fetch_google_sheets_data',
                sheet_url: sheetUrl,
                sheet_name: sheetName,
                nonce: excelEmbedAjax.nonce
            },
            success: function(response) {
                if (response.success && response.data) {
                    displayPreviewTable($content, response.data);
                } else {
                    $content.html('<p class="error">Error loading preview: ' + (response.data || 'Unknown error') + '</p>');
                }
            },
            error: function() {
                $content.html('<p class="error">Failed to load preview. Please check the URL and try again.</p>');
            },
            complete: function() {
                $loading.hide();
                $button.prop('disabled', false).text('Preview Data');
            }
        });
    });
    
    // Display preview table
    function displayPreviewTable($container, data) {
        if (!data || data.length === 0) {
            $container.html('<p>No data found in sheet.</p>');
            return;
        }
        
        var table = '<div class="excel-preview-table-wrapper">';
        table += '<table class="excel-preview-table">';
        
        // Add header row
        if (data.length > 0) {
            table += '<thead><tr>';
            data[0].forEach(function(cell, index) {
                table += '<th>' + (cell || 'Column ' + (index + 1)) + '</th>';
            });
            table += '</tr></thead>';
        }
        
        // Add data rows (limit to first 10 for preview)
        table += '<tbody>';
        var maxRows = Math.min(data.length, 10);
        for (var i = 1; i < maxRows; i++) {
            table += '<tr>';
            data[i].forEach(function(cell) {
                table += '<td>' + (cell || '') + '</td>';
            });
            table += '</tr>';
        }
        table += '</tbody>';
        table += '</table>';
        
        if (data.length > 10) {
            table += '<p class="excel-preview-note">Showing first 10 rows of ' + data.length + ' total rows.</p>';
        }
        
        table += '</div>';
        
        $container.html(table);
    }
    
    // Auto-preview when URL changes (with debounce)
    var previewTimeout;
    $(document).on('input', '.excel-sheet-url', function() {
        clearTimeout(previewTimeout);
        previewTimeout = setTimeout(function() {
            var $field = $(this).closest('.acf-excel-embed-field');
            var $previewContainer = $field.find('.excel-preview-container');
            
            // Only auto-preview if preview is already visible
            if ($previewContainer.is(':visible')) {
                $field.find('.preview-excel-data').click();
            }
        }.bind(this), 1000);
    });
    
    // Validate Google Sheets URL format
    $(document).on('blur', '.excel-sheet-url', function() {
        var url = $(this).val();
        if (url && !url.match(/docs\.google\.com\/spreadsheets\/d\//)) {
            $(this).addClass('error');
            $(this).after('<p class="description error">Please enter a valid Google Sheets URL.</p>');
        } else {
            $(this).removeClass('error');
            $(this).siblings('.description.error').remove();
        }
    });
    
    // Toggle preview visibility
    $(document).on('click', '.excel-preview-toggle', function(e) {
        e.preventDefault();
        var $container = $(this).siblings('.excel-preview-container');
        $container.toggle();
        
        if ($container.is(':visible')) {
            $(this).text('Hide Preview');
        } else {
            $(this).text('Show Preview');
        }
    });
    
}); 