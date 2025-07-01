jQuery(document).ready(function($) {
    
    // Handle dynamic loading of Excel data via AJAX
    $('.excel-embed-container[data-sheet-url]').each(function() {
        var $container = $(this);
        var sheetUrl = $container.data('sheet-url');
        var sheetName = $container.data('sheet-name') || '';
        
        if (!sheetUrl) return;
        
        // Show loading state
        $container.html('<div class="excel-loading">Loading spreadsheet data...</div>');
        
        // Fetch data via AJAX
        $.ajax({
            url: excelEmbedAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'fetch_google_sheets_data',
                sheet_url: sheetUrl,
                sheet_name: sheetName,
                nonce: excelEmbedAjax.nonce
            },
            success: function(response) {
                if (response.success && response.data) {
                    renderExcelTable($container, response.data);
                } else {
                    $container.html('<div class="excel-error">Error loading data: ' + (response.data || 'Unknown error') + '</div>');
                }
            },
            error: function() {
                $container.html('<div class="excel-error">Failed to load spreadsheet data. Please check the URL and try again.</div>');
            }
        });
    });
    
    function renderExcelTable($container, data) {
        if (!data || data.length === 0) {
            $container.html('<div class="excel-error">No data found in spreadsheet.</div>');
            return;
        }
        
        var config = {
            enableSearch: $container.data('enable-search') !== false,
            enableFilters: $container.data('enable-filters') !== false,
            showStatusIndicators: $container.data('show-status-indicators') !== false,
            rowsPerPage: parseInt($container.data('rows-per-page')) || 5
        };
        
        var html = '<div class="excel-embed-header">';
        html += '<h3 class="excel-embed-title">Spreadsheet Data</h3>';
        
        if (config.enableSearch || config.enableFilters) {
            html += '<div class="excel-embed-controls">';
            
            if (config.enableSearch) {
                html += '<div class="excel-search-box">';
                html += '<input type="text" placeholder="Search data..." />';
                html += '</div>';
            }
            
            if (config.enableFilters) {
                html += '<div class="excel-filter-dropdown">';
                html += '<select><option value="">All Status</option>';
                html += '<option value="done">Done</option>';
                html += '<option value="pending">Pending</option>';
                html += '<option value="verified">Verified</option>';
                html += '<option value="in progress">In Progress</option>';
                html += '</select>';
                html += '</div>';
            }
            
            html += '</div>';
        }
        
        html += '</div>';
        
        html += '<div class="excel-table-wrapper">';
        html += '<table class="excel-table">';
        
        // Header
        if (data.length > 0) {
            html += '<thead><tr>';
            data[0].forEach(function(cell) {
                html += '<th>' + (cell || 'Column') + '</th>';
            });
            html += '</tr></thead>';
        }
        
        // Body
        html += '<tbody>';
        // Only show first page of data initially
        var startIndex = 1;
        var endIndex = Math.min(startIndex + config.rowsPerPage - 1, data.length - 1);
        for (var i = startIndex; i <= endIndex; i++) {
            html += '<tr>';
            data[i].forEach(function(cell) {
                html += '<td>' + (cell || '') + '</td>';
            });
            html += '</tr>';
        }
        html += '</tbody>';
        html += '</table>';
        html += '</div>';
        
        // Pagination
        html += '<div class="excel-pagination">';
        html += '<div class="excel-pagination-info">Page 1 of ' + Math.ceil((data.length - 1) / config.rowsPerPage) + ' (' + (data.length - 1) + ' total entries)</div>';
        html += '<div class="excel-pagination-controls"></div>';
        html += '</div>';
        
        $container.html(html);
        
        // Initialize the Excel embed functionality for the newly loaded table
        initializeExcelEmbed($container, data);
    }
    
    function initializeExcelEmbed($container, fullData) {
        var $table = $container.find('.excel-table');
        var $searchBox = $container.find('.excel-search-box input');
        var $filterDropdown = $container.find('.excel-filter-dropdown select');
        var $pagination = $container.find('.excel-pagination');
        
        if ($table.length === 0) return;
        
        var originalData = [];
        var filteredData = [];
        var currentPage = 1;
        var rowsPerPage = parseInt($container.data('rows-per-page')) || 5;
        var searchTerm = '';
        var statusFilter = '';
        
        // Store original data from the full dataset (skip header row)
        if (fullData && fullData.length > 1) {
            for (var i = 1; i < fullData.length; i++) {
                originalData.push(fullData[i]);
            }
        }
        
        filteredData = originalData.slice();
        
        // Initialize
        setupEventListeners();
        updateTable();
        updatePagination();
        
        function setupEventListeners() {
            // Search functionality
            $searchBox.on('input', function() {
                searchTerm = $(this).val().toLowerCase();
                currentPage = 1;
                filterData();
                updateTable();
                updatePagination();
            });
            
            // Status filter
            $filterDropdown.on('change', function() {
                statusFilter = $(this).val();
                currentPage = 1;
                filterData();
                updateTable();
                updatePagination();
            });
            
            // Pagination
            $pagination.on('click', '.excel-pagination-btn', function(e) {
                e.preventDefault();
                var action = $(this).data('action');
                
                switch(action) {
                    case 'prev':
                        if (currentPage > 1) {
                            currentPage--;
                            updateTable();
                            updatePagination();
                        }
                        break;
                    case 'next':
                        var maxPages = Math.ceil(filteredData.length / rowsPerPage);
                        if (currentPage < maxPages) {
                            currentPage++;
                            updateTable();
                            updatePagination();
                        }
                        break;
                    case 'page':
                        currentPage = parseInt($(this).data('page'));
                        updateTable();
                        updatePagination();
                        break;
                }
            });
        }
        
        function filterData() {
            filteredData = originalData.filter(function(row) {
                var matchesSearch = true;
                var matchesStatus = true;
                
                // Search filter
                if (searchTerm) {
                    matchesSearch = row.some(function(cell) {
                        return cell.toLowerCase().includes(searchTerm);
                    });
                }
                
                // Status filter
                if (statusFilter) {
                    matchesStatus = row.some(function(cell) {
                        return cell.toLowerCase().includes(statusFilter.toLowerCase());
                    });
                }
                
                return matchesSearch && matchesStatus;
            });
        }
        
        function updateTable() {
            var startIndex = (currentPage - 1) * rowsPerPage;
            var endIndex = startIndex + rowsPerPage;
            var pageData = filteredData.slice(startIndex, endIndex);
            
            var tbody = $table.find('tbody');
            tbody.empty();
            
            pageData.forEach(function(rowData) {
                var row = $('<tr></tr>');
                
                rowData.forEach(function(cellData, index) {
                    var cell = $('<td></td>');
                    
                    // Check if this is a status column (common status values)
                    if (isStatusColumn(cellData)) {
                        cell.html(formatStatusBadge(cellData));
                    } else {
                        cell.text(cellData);
                    }
                    
                    row.append(cell);
                });
                
                tbody.append(row);
            });
            
            // Update row count display
            updateRowCount();
        }
        
        function updatePagination() {
            var totalRows = filteredData.length;
            var maxPages = Math.ceil(totalRows / rowsPerPage);
            
            var $controls = $pagination.find('.excel-pagination-controls');
            $controls.empty();
            
            // Previous button
            var $prevBtn = $('<button class="excel-pagination-btn" data-action="prev">← Previous</button>');
            if (currentPage <= 1) {
                $prevBtn.prop('disabled', true);
            }
            $controls.append($prevBtn);
            
            // Page numbers
            var startPage = Math.max(1, currentPage - 2);
            var endPage = Math.min(maxPages, currentPage + 2);
            
            for (var i = startPage; i <= endPage; i++) {
                var $pageBtn = $('<button class="excel-pagination-btn" data-action="page" data-page="' + i + '">' + i + '</button>');
                if (i === currentPage) {
                    $pageBtn.addClass('active');
                }
                $controls.append($pageBtn);
            }
            
            // Next button
            var $nextBtn = $('<button class="excel-pagination-btn" data-action="next">Next →</button>');
            if (currentPage >= maxPages) {
                $nextBtn.prop('disabled', true);
            }
            $controls.append($nextBtn);
        }
        
        function updateRowCount() {
            var totalRows = filteredData.length;
            var maxPages = Math.ceil(totalRows / rowsPerPage);
            
            var $info = $pagination.find('.excel-pagination-info');
            if (totalRows === 0) {
                $info.text('No results found');
            } else {
                $info.text('Page ' + currentPage + ' of ' + maxPages + ' (' + totalRows + ' total entries)');
            }
        }
        
        function isStatusColumn(cellData) {
            var statusValues = ['done', 'pending', 'verified', 'in progress', 'completed', 'active', 'inactive'];
            return statusValues.some(function(status) {
                return cellData.toLowerCase().includes(status);
            });
        }
        
        function formatStatusBadge(status) {
            var statusLower = status.toLowerCase();
            var badgeClass = 'excel-status';
            
            if (statusLower.includes('done') || statusLower.includes('completed')) {
                badgeClass += ' status-done';
            } else if (statusLower.includes('verified')) {
                badgeClass += ' status-verified';
            } else if (statusLower.includes('pending')) {
                badgeClass += ' status-pending';
            } else if (statusLower.includes('in progress') || statusLower.includes('active')) {
                badgeClass += ' status-in-progress';
            }
            
            return '<span class="' + badgeClass + '">' + status + '</span>';
        }
    }
    
    // Export functionality
    $(document).on('click', '.excel-export-btn', function(e) {
        e.preventDefault();
        
        var $container = $(this).closest('.excel-embed-container');
        var $table = $container.find('.excel-table');
        
        // Create CSV content
        var csv = [];
        $table.find('tr').each(function() {
            var row = [];
            $(this).find('th, td').each(function() {
                var cell = $(this).text().replace(/"/g, '""');
                row.push('"' + cell + '"');
            });
            csv.push(row.join(','));
        });
        
        // Download CSV
        var csvContent = csv.join('\n');
        var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        var url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'spreadsheet_data.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
    
}); 