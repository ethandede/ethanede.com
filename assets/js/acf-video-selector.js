(function($) {
    'use strict';

    // Debug function
    function debugLog(message, data) {
        if (window.console && console.log) {
            console.log('[ACF Video Selector] ' + message, data || '');
        }
    }

    // Track initialization to prevent duplicates
    let isInitialized = false;
    
    // Initialize when ACF is ready
    acf.add_action('ready_field/type=repeater', function(field) {
        if (field.get('name') === 'video_poster_frames' && !isInitialized) {
            debugLog('Initializing video selector for poster frames repeater');
            isInitialized = true;
            initVideoSelector(field);
        }
    });

    // Also initialize when new repeater rows are added
    acf.add_action('append_field/type=repeater', function(field) {
        if (field.get('name') === 'video_poster_frames') {
            debugLog('New repeater row added, updating selectors');
            // Small delay to let ACF finish rendering
            setTimeout(function() {
                updateVideoSelectorsInRepeater(field);
            }, 100);
        }
    });

    // Initialize when media gallery changes
    acf.add_action('change_field/name=deliverable_media', function(field) {
        debugLog('Media gallery changed, updating video selectors');
        setTimeout(function() {
            updateVideoSelectors();
        }, 300);
    });

    function initVideoSelector(repeaterField) {
        // Update video selectors in this repeater
        updateVideoSelectorsInRepeater(repeaterField);
    }

    function updateVideoSelectors() {
        debugLog('Updating all video selectors');
        $('.acf-field[data-name="video_selection"] select').each(function() {
            const $select = $(this);
            debugLog('Updating selector:', $select.attr('name'));
            updateVideoSelectorOptions($select);
        });
    }

    function updateVideoSelectorsInRepeater(repeaterField) {
        debugLog('Updating video selectors in specific repeater');
        repeaterField.$el.find('.acf-field[data-name="video_selection"] select').each(function() {
            const $select = $(this);
            debugLog('Updating selector in repeater:', $select.attr('name'));
            updateVideoSelectorOptions($select);
        });
    }

    function updateVideoSelectorOptions($select) {
        // Get the media gallery field - try multiple approaches
        let mediaField = acf.getField('field_deliverable_media');
        if (!mediaField) {
            mediaField = acf.getField('deliverable_media');
        }
        if (!mediaField) {
            // Try finding by data attribute
            const $mediaFieldElement = $('[data-name="deliverable_media"]');
            if ($mediaFieldElement.length) {
                mediaField = acf.getField($mediaFieldElement);
            }
        }
        
        debugLog('Media field found:', !!mediaField);
        if (!mediaField) {
            debugLog('Could not find media gallery field');
            return;
        }

        // Get current selection to preserve it
        const currentValue = $select.val();
        
        // Clear existing dynamic options but preserve server-side options
        $select.find('option[data-dynamic="true"]').remove();
        
        // Ensure we have a placeholder option
        if ($select.find('option[value=""]').length === 0) {
            $select.prepend('<option value="">Select a video...</option>');
        }

        // Get media gallery data
        const mediaData = mediaField.val();
        debugLog('Media data:', mediaData);
        
        if (!mediaData || !Array.isArray(mediaData)) {
            debugLog('No media data or not an array');
            // Add helpful message when no media is available
            $select.append('<option value="" disabled>No videos found in media gallery</option>');
            return;
        }

        // Check if we have attachment IDs instead of full objects
        let needsAttachmentData = false;
        if (mediaData.length > 0 && typeof mediaData[0] === 'number') {
            needsAttachmentData = true;
            debugLog('Media data contains attachment IDs, fetching full data...');
        }

        if (needsAttachmentData) {
            // Fetch attachment data via AJAX
            const attachmentIds = mediaData;
            
            $.ajax({
                url: ajaxurl || '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action: 'get_attachment_data',
                    attachment_ids: attachmentIds,
                    nonce: acf.get('nonce') || ''
                },
                success: function(response) {
                    if (response.success && response.data) {
                        processMediaItems($select, response.data, currentValue);
                    } else {
                        // Fallback: try to get data from ACF's internal cache
                        processAttachmentIds($select, attachmentIds, currentValue);
                    }
                },
                error: function() {
                    debugLog('AJAX failed, trying fallback method');
                    processAttachmentIds($select, attachmentIds, currentValue);
                }
            });
        } else {
            // Process full media objects directly
            processMediaItems($select, mediaData, currentValue);
        }

    }

    function processMediaItems($select, mediaItems, currentValue) {
        debugLog('Processing media items:', mediaItems);
        
        // Don't clear all options, just clear dynamic ones (preserve any existing server-side options)
        $select.find('option[data-dynamic="true"]').remove();
        
        // If no placeholder option exists, add one
        if ($select.find('option[value=""]').length === 0) {
            $select.prepend('<option value="">Select a video...</option>');
        }
        
        let videoCount = 0;
        mediaItems.forEach(function(item, index) {
            debugLog('Processing media item ' + index + ':', item);
            
            if (item && (item.type === 'video' || (item.mime_type && item.mime_type.startsWith('video/')))) {
                videoCount++;
                const filename = item.filename || item.name || 'video-' + videoCount + '.mp4';
                const title = item.title || item.alt || filename;
                
                debugLog('Found video:', {filename: filename, title: title});
                
                // Check if this option already exists (from server-side)
                if ($select.find('option[value="' + filename + '"]').length === 0) {
                    // Create a more descriptive option text
                    let optionText = title;
                    if (title !== filename) {
                        optionText += ' (' + filename + ')';
                    }
                    
                    $select.append(
                        '<option value="' + filename + '" title="' + filename + '" data-dynamic="true">' + optionText + '</option>'
                    );
                }
            }
        });

        debugLog('Total videos found:', videoCount);

        // Show message if no videos found
        if (videoCount === 0 && $select.find('option').length <= 1) {
            $select.append('<option value="" disabled data-dynamic="true">No videos found - upload videos to the media gallery above</option>');
        }

        // Restore previous selection if it still exists
        if (currentValue && $select.find('option[value="' + currentValue + '"]').length > 0) {
            $select.val(currentValue);
        }

        // Trigger change event to update ACF
        $select.trigger('change');
        
        // Add visual feedback for configured poster frames
        updateRowVisualFeedback($select);
    }

    function processAttachmentIds($select, attachmentIds, currentValue) {
        debugLog('Processing attachment IDs with fallback method:', attachmentIds);
        
        let videoCount = 0;
        
        // Try to get attachment data from WordPress media library
        attachmentIds.forEach(function(attachmentId, index) {
            // Try to get attachment data from ACF's attachment cache
            const attachment = acf.get('attachment_' + attachmentId);
            
            if (attachment) {
                debugLog('Found cached attachment data:', attachment);
                
                if (attachment.type === 'video' || (attachment.mime && attachment.mime.startsWith('video/'))) {
                    videoCount++;
                    const filename = attachment.filename || attachment.name || 'video-' + videoCount + '.mp4';
                    const title = attachment.title || attachment.alt || filename;
                    
                    debugLog('Found video from cache:', {filename: filename, title: title});
                    
                    let optionText = title;
                    if (title !== filename) {
                        optionText += ' (' + filename + ')';
                    }
                    
                    $select.append(
                        '<option value="' + filename + '" title="' + filename + '">' + optionText + '</option>'
                    );
                }
            } else {
                // Fallback: create a generic option based on attachment ID
                debugLog('No cached data for attachment ' + attachmentId + ', creating generic option');
                $select.append(
                    '<option value="attachment-' + attachmentId + '">Video (ID: ' + attachmentId + ')</option>'
                );
                videoCount++;
            }
        });

        debugLog('Total videos found (fallback):', videoCount);

        if (videoCount === 0) {
            $select.append('<option value="" disabled>No videos found - upload videos to the media gallery above</option>');
        }

        // Restore previous selection if it still exists
        if (currentValue && $select.find('option[value="' + currentValue + '"]').length > 0) {
            $select.val(currentValue);
        }

        $select.trigger('change');
        updateRowVisualFeedback($select);
    }

    function updateRowVisualFeedback($select) {
        const $row = $select.closest('.acf-row');
        const hasVideo = $select.val() !== '';
        const $posterField = $row.find('[data-name="poster_image"]');
        const hasPoster = $posterField.find('img').length > 0;
        
        if (hasVideo && hasPoster) {
            $row.addClass('poster-configured');
        } else {
            $row.removeClass('poster-configured');
        }
    }

    // Add refresh button and debug panel (only once)
    function addRefreshButton() {
        const $posterFramesField = $('.acf-field[data-name="video_poster_frames"]');
        if ($posterFramesField.length === 0) return;
        
        // Only add buttons if they don't already exist
        if ($posterFramesField.find('.video-controls').length === 0) {
            const $label = $posterFramesField.find('.acf-label:first');
            $label.append(
                '<div class="video-controls" style="display: inline-block; margin-left: 15px;">' +
                '<button type="button" class="button button-secondary video-refresh-btn">' +
                'Refresh Videos</button> ' +
                '<button type="button" class="button button-secondary video-debug-btn">' +
                'Debug Info</button>' +
                '</div>'
            );
        }
    }
    
    // Use event delegation for button clicks (only bind once)
    $(document).off('click.video-selector').on('click.video-selector', '.video-refresh-btn', function(e) {
        e.preventDefault();
        debugLog('Manual refresh triggered');
        updateVideoSelectors();
    });
    
    $(document).off('click.video-selector').on('click.video-selector', '.video-debug-btn', function(e) {
        e.preventDefault();
        showDebugInfo();
    });
    
    function showDebugInfo() {
        // Remove existing debug panel
        $('.video-debug-panel').remove();
        
        // Get media field info
        let mediaField = acf.getField('field_deliverable_media') || acf.getField('deliverable_media');
        const mediaData = mediaField ? mediaField.val() : null;
        
        let debugHtml = '<div class="video-debug-panel" style="background: #f0f0f0; border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 4px;">';
        debugHtml += '<h4>Video Debug Information</h4>';
        debugHtml += '<p><strong>Media Field Found:</strong> ' + (mediaField ? 'Yes' : 'No') + '</p>';
        debugHtml += '<p><strong>Media Data Type:</strong> ' + (Array.isArray(mediaData) ? 'Array' : typeof mediaData) + '</p>';
        debugHtml += '<p><strong>Media Items Count:</strong> ' + (Array.isArray(mediaData) ? mediaData.length : 'N/A') + '</p>';
        
        if (Array.isArray(mediaData) && mediaData.length > 0) {
            debugHtml += '<h5>Media Items:</h5><ul>';
            mediaData.forEach(function(item, index) {
                debugHtml += '<li><strong>Item ' + index + ':</strong> ';
                
                if (typeof item === 'number') {
                    debugHtml += 'Attachment ID: ' + item;
                } else if (typeof item === 'object') {
                    debugHtml += 'Type: ' + (item.type || 'unknown') + ', ';
                    debugHtml += 'MIME: ' + (item.mime_type || 'unknown') + ', ';
                    debugHtml += 'Filename: ' + (item.filename || item.name || 'unknown');
                } else {
                    debugHtml += 'Unknown format: ' + typeof item;
                }
                debugHtml += '</li>';
            });
            debugHtml += '</ul>';
        }
        
        debugHtml += '<button type="button" class="button" onclick="$(this).closest(\'.video-debug-panel\').remove();">Close</button>';
        debugHtml += '</div>';
        
        $('.acf-field[data-name="video_poster_frames"]').prepend(debugHtml);
    }

    // Initialize on page load with better timing
    $(document).ready(function() {
        debugLog('Document ready, initializing...');
        
        // Wait for ACF to be fully loaded before adding buttons
        if (typeof acf !== 'undefined') {
            addRefreshButton();
            
            // Single delayed update
            setTimeout(function() {
                debugLog('Initial update after ACF ready');
                updateVideoSelectors();
            }, 1000);
        } else {
            // ACF not ready yet, wait longer
            setTimeout(function() {
                addRefreshButton();
                updateVideoSelectors();
            }, 2000);
        }
    });
    
    // Also initialize when ACF admin page loads
    if (typeof acf !== 'undefined') {
        acf.add_action('ready', function() {
            debugLog('ACF ready action triggered');
            // Only add buttons if not already added
            setTimeout(addRefreshButton, 100);
        });
    }

})(jQuery); 