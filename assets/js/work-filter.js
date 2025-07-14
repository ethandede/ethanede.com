document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const searchInput = document.getElementById('work-search');
    const workList = document.querySelector('.work-list');
    const cards = document.querySelectorAll('.card--work');
    const resetButton = document.querySelector('.reset-filters');
    const activeFiltersContainer = document.querySelector('.active-filters-compact');
    const activeFiltersList = document.querySelector('.active-filters-list');
    const tabTriggers = document.querySelectorAll('.tab-trigger');
    const tabContents = document.querySelectorAll('.tab-content');

    // Filter state
    let filters = {
        search: '',
        format: [],
        'content-type': [],
        project: [],
        technology: [],
        company: []
    };

    // Initialize tabs - open first tab by default
    function initializeTabs() {
        if (tabTriggers.length > 0) {
            const firstTab = tabTriggers[0];
            const firstContent = document.getElementById(firstTab.dataset.tab + '-content');
            
            firstTab.classList.add('active');
            firstContent.classList.add('active');
        }
    }

    // Tab functionality
    function setupTabs() {
        tabTriggers.forEach(trigger => {
            trigger.addEventListener('click', function() {
                const tabId = this.dataset.tab;
                const content = document.getElementById(tabId + '-content');
                
                // Close all tabs
                tabTriggers.forEach(t => t.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                // Open clicked tab
                this.classList.add('active');
                content.classList.add('active');
            });
        });
    }

    // Search functionality with debounce
    let searchTimeout;
    function setupSearch() {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filters.search = this.value.toLowerCase().trim();
                applyFilters();
                
                // Debug: Log search term and results count
                if (filters.search) {
                    const visibleCards = document.querySelectorAll('.card--work[style*="display: block"]');
                    console.log(`Search for "${filters.search}" found ${visibleCards.length} results`);
                }
            }, 300);
        });
    }

    // Checkbox functionality
    function setupCheckboxes() {
        const checkboxes = document.querySelectorAll('.checkbox-item input[type="checkbox"]');
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const filterType = this.name;
                const value = this.value;
                
                if (this.checked) {
                    if (!filters[filterType].includes(value)) {
                        filters[filterType].push(value);
                    }
                } else {
                    filters[filterType] = filters[filterType].filter(v => v !== value);
                }
                
                applyFilters();
            });
        });
    }

    // Reset functionality
    function setupReset() {
        resetButton.addEventListener('click', function() {
            // Reset search
            searchInput.value = '';
            filters.search = '';
            
            // Reset checkboxes
            const checkboxes = document.querySelectorAll('.checkbox-item input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Reset filter state
            filters = {
                search: '',
                format: [],
                'content-type': [],
                project: [],
                technology: [],
                company: []
            };
            
            applyFilters();
        });
    }

    // Calculate counts for each filter option
    function calculateFilterCounts() {
        const counts = {
            format: { project: 0, deliverable: 0 },
            'content-type': {},
            project: {},
            technology: {},
            company: {}
        };

        // Get all checkboxes to build the count structure
        const checkboxes = document.querySelectorAll('.checkbox-item input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            const filterType = checkbox.name;
            const value = checkbox.value;
            
            if (filterType === 'format') {
                counts.format[value] = 0;
            } else {
                if (!counts[filterType][value]) {
                    counts[filterType][value] = 0;
                }
            }
        });

        // Count cards for each filter option
        cards.forEach(card => {
            const cardData = getCardData(card);
            
            // Count for format - only if card would be visible with other current filters
            if (cardData.format) {
                const wouldBeVisible = wouldCardBeVisibleWithFilter(cardData, 'format', cardData.format);
                if (wouldBeVisible) {
                    counts.format[cardData.format]++;
                }
            }
            
            // Count for content-type
            if (cardData['content-type']) {
                if (!counts['content-type'][cardData['content-type']]) {
                    counts['content-type'][cardData['content-type']] = 0;
                }
                const wouldBeVisible = wouldCardBeVisibleWithFilter(cardData, 'content-type', cardData['content-type']);
                if (wouldBeVisible) {
                    counts['content-type'][cardData['content-type']]++;
                }
            }
            
            // Count for project
            if (cardData.project) {
                if (!counts.project[cardData.project]) {
                    counts.project[cardData.project] = 0;
                }
                const wouldBeVisible = wouldCardBeVisibleWithFilter(cardData, 'project', cardData.project);
                if (wouldBeVisible) {
                    counts.project[cardData.project]++;
                }
            }
            
            // Count for technologies
            cardData.technologies.forEach(tech => {
                if (!counts.technology[tech]) {
                    counts.technology[tech] = 0;
                }
                const wouldBeVisible = wouldCardBeVisibleWithFilter(cardData, 'technology', tech);
                if (wouldBeVisible) {
                    counts.technology[tech]++;
                }
            });
            
            // Count for companies
            cardData.companies.forEach(company => {
                if (!counts.company[company]) {
                    counts.company[company] = 0;
                }
                const wouldBeVisible = wouldCardBeVisibleWithFilter(cardData, 'company', company);
                if (wouldBeVisible) {
                    counts.company[company]++;
                }
            });
        });

        return counts;
    }

    // Check if card would be visible with current filters (excluding the specified filter)
    function wouldCardBeVisibleWithFilter(cardData, excludeFilterType, excludeFilterValue) {
        // Create a copy of current filters
        const testFilters = JSON.parse(JSON.stringify(filters));
        
        // Remove the filter we're testing from the test filters
        if (excludeFilterType === 'search') {
            testFilters.search = '';
        } else {
            testFilters[excludeFilterType] = testFilters[excludeFilterType].filter(v => v !== excludeFilterValue);
        }
        
        // Check if card would be visible with these test filters
        return matchesFiltersWithState(cardData, testFilters);
    }

    // Check if card matches filters with a specific filter state
    function matchesFiltersWithState(cardData, filterState) {
        // Search filter
        if (filterState.search) {
            const searchText = cardData.title + ' ' + cardData.excerpt + ' ' + cardData.searchableContent;
            if (!searchText.includes(filterState.search)) {
                return false;
            }
        }
        
        // Format filter - OR logic (show if any selected format matches)
        if (filterState.format.length > 0) {
            const hasMatchingFormat = filterState.format.includes(cardData.format);
            if (!hasMatchingFormat) {
                return false;
            }
        }
        
        // Content type filter - OR logic (show if any selected content type matches)
        if (filterState['content-type'].length > 0) {
            const hasMatchingContentType = filterState['content-type'].includes(cardData['content-type']);
            if (!hasMatchingContentType) {
                return false;
            }
        }
        
        // Project filter - OR logic (show if any selected project matches)
        if (filterState.project.length > 0) {
            const hasMatchingProject = filterState.project.includes(cardData.project);
            if (!hasMatchingProject) {
                return false;
            }
        }
        
        // Technology filter - OR logic (show if any selected technology matches)
        if (filterState.technology.length > 0) {
            const hasMatchingTech = filterState.technology.some(tech => 
                cardData.technologies.includes(tech)
            );
            if (!hasMatchingTech) {
                return false;
            }
        }
        
        // Company filter - OR logic (show if any selected company matches)
        if (filterState.company.length > 0) {
            const hasMatchingCompany = filterState.company.some(company => 
                cardData.companies.includes(company)
            );
            if (!hasMatchingCompany) {
                return false;
            }
        }
        
        return true;
    }

    // Update filter option counts
    function updateFilterCounts() {
        const counts = calculateFilterCounts();
        
        // Update format counts
        Object.keys(counts.format).forEach(format => {
            const checkbox = document.querySelector(`input[name="format"][value="${format}"]`);
            if (checkbox) {
                const label = checkbox.closest('.checkbox-item');
                const countSpan = label.querySelector('.filter-count');
                if (countSpan) {
                    countSpan.textContent = counts.format[format];
                } else {
                    const newCountSpan = document.createElement('span');
                    newCountSpan.className = 'filter-count';
                    newCountSpan.textContent = counts.format[format];
                    label.appendChild(newCountSpan);
                }
            }
        });

        // Update other filter counts
        ['content-type', 'project', 'technology', 'company'].forEach(filterType => {
            Object.keys(counts[filterType]).forEach(value => {
                const checkbox = document.querySelector(`input[name="${filterType}"][value="${value}"]`);
                if (checkbox) {
                    const label = checkbox.closest('.checkbox-item');
                    const countSpan = label.querySelector('.filter-count');
                    if (countSpan) {
                        countSpan.textContent = counts[filterType][value];
                    } else {
                        const newCountSpan = document.createElement('span');
                        newCountSpan.className = 'filter-count';
                        newCountSpan.textContent = counts[filterType][value];
                        label.appendChild(newCountSpan);
                    }
                }
            });
        });
    }

    // Apply filters to cards
    function applyFilters() {
        let visibleCount = 0;
        
        cards.forEach(card => {
            const cardData = getCardData(card);
            const isVisible = matchesFilters(cardData);
            
            if (isVisible) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        updateActiveFilters();
        updateEmptyState(visibleCount);
        updateFilterCounts();
    }

    // Get card data for filtering
    function getCardData(card) {
        return {
            title: card.querySelector('.card__title')?.textContent?.toLowerCase() || '',
            excerpt: card.querySelector('.card__description')?.textContent?.toLowerCase() || '',
            searchableContent: card.dataset.searchableContent?.toLowerCase() || '',
            format: card.dataset.format || '',
            'content-type': card.dataset.contentType || '',
            project: card.dataset.projects || '',
            technologies: card.dataset.technologies ? card.dataset.technologies.split(',').map(t => t.trim()) : [],
            companies: card.dataset.companies ? card.dataset.companies.split(',').map(c => c.trim()) : []
        };
    }

    // Check if card matches current filters
    function matchesFilters(cardData) {
        // Search filter
        if (filters.search) {
            const searchText = cardData.title + ' ' + cardData.excerpt + ' ' + cardData.searchableContent;
            if (!searchText.includes(filters.search)) {
                return false;
            }
        }
        
        // Format filter - OR logic (show if any selected format matches)
        if (filters.format.length > 0) {
            const hasMatchingFormat = filters.format.includes(cardData.format);
            if (!hasMatchingFormat) {
                return false;
            }
        }
        
        // Content type filter - OR logic (show if any selected content type matches)
        if (filters['content-type'].length > 0) {
            const hasMatchingContentType = filters['content-type'].includes(cardData['content-type']);
            if (!hasMatchingContentType) {
                return false;
            }
        }
        
        // Project filter - OR logic (show if any selected project matches)
        if (filters.project.length > 0) {
            const hasMatchingProject = filters.project.includes(cardData.project);
            if (!hasMatchingProject) {
                return false;
            }
        }
        
        // Technology filter - OR logic (show if any selected technology matches)
        if (filters.technology.length > 0) {
            const hasMatchingTech = filters.technology.some(tech => 
                cardData.technologies.includes(tech)
            );
            if (!hasMatchingTech) {
                return false;
            }
        }
        
        // Company filter - OR logic (show if any selected company matches)
        if (filters.company.length > 0) {
            const hasMatchingCompany = filters.company.some(company => 
                cardData.companies.includes(company)
            );
            if (!hasMatchingCompany) {
                return false;
            }
        }
        
        return true;
    }

    // Update active filters display
    function updateActiveFilters() {
        const activeFilters = [];
        
        // Collect all active filters
        Object.keys(filters).forEach(filterType => {
            if (filterType === 'search' && filters[filterType]) {
                activeFilters.push({
                    type: 'search',
                    label: `Search: "${filters[filterType]}"`,
                    value: filters[filterType]
                });
            } else if (filters[filterType].length > 0) {
                filters[filterType].forEach(value => {
                    const label = getFilterLabel(filterType, value);
                    activeFilters.push({
                        type: filterType,
                        label: label,
                        value: value
                    });
                });
            }
        });
        
        // Update display
        if (activeFilters.length > 0) {
            activeFiltersContainer.style.display = 'block';
            activeFiltersList.innerHTML = activeFilters.map(filter => `
                <span class="active-filter-tag" data-type="${filter.type}" data-value="${filter.value}">
                    ${filter.label}
                    <span class="remove-filter" onclick="removeFilter('${filter.type}', '${filter.value}')">
                        <i class="fas fa-times"></i>
                    </span>
                </span>
            `).join('');
        } else {
            activeFiltersContainer.style.display = 'none';
        }
    }

    // Get human-readable filter label
    function getFilterLabel(filterType, value) {
        switch (filterType) {
            case 'format':
                return value === 'project' ? 'Project' : 'Deliverable';
            case 'content-type':
                return value.charAt(0).toUpperCase() + value.slice(1).replace('-', ' ');
            case 'project':
                const projectCard = document.querySelector(`[data-projects="${value}"]`);
                return projectCard ? projectCard.querySelector('.card__title')?.textContent || 'Project' : 'Project';
            case 'technology':
                return value.charAt(0).toUpperCase() + value.slice(1);
            case 'company':
                return value.charAt(0).toUpperCase() + value.slice(1);
            default:
                return value;
        }
    }

    // Remove individual filter
    window.removeFilter = function(filterType, value) {
        if (filterType === 'search') {
            searchInput.value = '';
            filters.search = '';
        } else {
            filters[filterType] = filters[filterType].filter(v => v !== value);
            
            // Uncheck corresponding checkbox
            const checkbox = document.querySelector(`input[name="${filterType}"][value="${value}"]`);
            if (checkbox) {
                checkbox.checked = false;
            }
        }
        
        applyFilters();
    };

    // Update empty state
    function updateEmptyState(visibleCount) {
        let emptyState = workList.querySelector('.empty-state');
        
        if (visibleCount === 0) {
            if (!emptyState) {
                emptyState = document.createElement('div');
                emptyState.className = 'empty-state';
                emptyState.innerHTML = `
                    <i class="fas fa-search"></i>
                    <p>No results found. Try adjusting your filters.</p>
                `;
                workList.appendChild(emptyState);
            }
        } else if (emptyState) {
            emptyState.remove();
        }
    }

    // Initialize everything
    function init() {
        initializeTabs();
        setupTabs();
        setupSearch();
        setupCheckboxes();
        setupReset();
        applyFilters();
    }

    // Start the filter system
    init();
}); 