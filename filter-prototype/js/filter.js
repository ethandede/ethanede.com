// Sample data structure
const sampleData = {
  projects: [
    {
      id: 1,
      title: "E-commerce Platform Redesign",
      type: "web-development",
      description: "Complete redesign and development of a modern e-commerce platform",
      image: "https://picsum.photos/seed/ecom/400/300",
      tags: ["React", "WordPress", "Frontend", "UI/UX"],
      company: {
        name: "TechCorp",
        logo: "https://picsum.photos/seed/techcorp/80/30"
      }
    },
    {
      id: 2,
      title: "Mobile Banking App",
      type: "application",
      description: "Secure and intuitive mobile banking application",
      image: "https://picsum.photos/seed/bank/400/300",
      tags: ["React Native", "Backend", "JavaScript", "Security"],
      company: {
        name: "FinanceHub",
        logo: "https://picsum.photos/seed/finance/80/30"
      }
    },
    {
      id: 3,
      title: "Healthcare Dashboard",
      type: "web-development",
      description: "Analytics dashboard for healthcare providers",
      image: "https://picsum.photos/seed/health/400/300",
      tags: ["Vue.js", "Frontend", "Data Visualization"],
      company: {
        name: "HealthTech",
        logo: "https://picsum.photos/seed/health/80/30"
      }
    }
  ],
  deliverables: [
    {
      id: 4,
      title: "User Authentication System",
      type: "backend",
      description: "Secure authentication and authorization system",
      image: "https://picsum.photos/seed/auth/400/300",
      tags: ["Backend", "Security", "API"],
      company: {
        name: "SecureNet",
        logo: "https://picsum.photos/seed/secure/80/30"
      }
    },
    {
      id: 5,
      title: "Product Landing Page",
      type: "website",
      description: "Conversion-optimized product landing page",
      image: "https://picsum.photos/seed/landing/400/300",
      tags: ["Frontend", "UI/UX", "WordPress"],
      company: {
        name: "MarketPro",
        logo: "https://picsum.photos/seed/market/80/30"
      }
    },
    {
      id: 6,
      title: "API Integration Service",
      type: "backend",
      description: "Third-party API integration and data processing",
      image: "https://picsum.photos/seed/api/400/300",
      tags: ["Backend", "API", "JavaScript"],
      company: {
        name: "DataFlow",
        logo: "https://picsum.photos/seed/data/80/30"
      }
    }
  ]
};

// Function to create a card element
function createCard(item) {
  const cardElement = document.createElement('div');
  cardElement.className = `card ${item.type === 'web-development' || item.type === 'application' ? 'card--project' : 'card--deliverable'}`;
  
  const tagsHtml = item.tags.map(tag => {
    const tagClass = 
      tag.includes('Frontend') || tag.includes('UI/UX') ? 'tag-skill' :
      tag.includes('Backend') || tag.includes('API') ? 'tag-technology' :
      tag.includes('React') || tag.includes('Vue.js') || tag.includes('WordPress') ? 'tag-project' :
      'tag-deliverable';
    
    return `<span class="tag ${tagClass}">${tag}</span>`;
  }).join('');

  cardElement.innerHTML = `
    <div class="card__link">
      <div class="card__overlay"></div>
      <div class="card__image-container">
        <img src="${item.image}" alt="${item.title}" class="card__image">
      </div>
      <div class="card__tags">
        ${tagsHtml}
      </div>
      <div class="card__content">
        <div class="card__content-main">
          <h3>${item.title}</h3>
          <p>${item.description}</p>
        </div>
        <div class="card__content-footer">
          <div class="card__company">
            <div class="company-info">
              <img src="${item.company.logo}" alt="${item.company.name}" class="company-logo-card">
              <span class="company-name">${item.company.name}</span>
            </div>
          </div>
          <div class="card__arrow">
            <i class="fas fa-arrow-right"></i>
          </div>
        </div>
      </div>
    </div>
  `;
  
  return cardElement;
}

// Function to render all items
function renderItems(items = [...sampleData.projects, ...sampleData.deliverables]) {
  const container = document.querySelector('.deliverables-grid');
  container.innerHTML = '';
  items.forEach(item => {
    container.appendChild(createCard(item));
  });
}

// Function to filter items
function filterItems() {
  const searchTerm = document.querySelector('.search-input').value.toLowerCase();
  const selectedTypes = Array.from(document.querySelectorAll('.checkbox-wrapper input:checked'))
    .map(checkbox => checkbox.value);
  const selectedTags = Array.from(document.querySelectorAll('.tags-cloud .tag.selected'))
    .map(tag => tag.textContent);

  let filteredItems = [...sampleData.projects, ...sampleData.deliverables];

  // Filter by search term
  if (searchTerm) {
    filteredItems = filteredItems.filter(item => 
      item.title.toLowerCase().includes(searchTerm) ||
      item.description.toLowerCase().includes(searchTerm) ||
      item.tags.some(tag => tag.toLowerCase().includes(searchTerm))
    );
  }

  // Filter by type
  if (selectedTypes.length > 0) {
    filteredItems = filteredItems.filter(item => 
      selectedTypes.includes(item.type)
    );
  }

  // Filter by tags
  if (selectedTags.length > 0) {
    filteredItems = filteredItems.filter(item =>
      selectedTags.every(tag => item.tags.includes(tag))
    );
  }

  renderItems(filteredItems);
}

// Initialize the page
document.addEventListener('DOMContentLoaded', () => {
  // Dropdown functionality
  const dropdowns = document.querySelectorAll('.filter-dropdown');
  
  dropdowns.forEach(dropdown => {
    const trigger = dropdown.querySelector('.dropdown-trigger');
    const content = dropdown.querySelector('.dropdown-content');
    
    trigger.addEventListener('click', () => {
      // Close other dropdowns
      dropdowns.forEach(other => {
        if (other !== dropdown) {
          other.querySelector('.dropdown-trigger').classList.remove('active');
          other.querySelector('.dropdown-content').classList.remove('active');
        }
      });
      
      // Toggle current dropdown
      trigger.classList.toggle('active');
      content.classList.toggle('active');
    });
  });
  
  // Close dropdowns when clicking outside
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.filter-dropdown')) {
      dropdowns.forEach(dropdown => {
        dropdown.querySelector('.dropdown-trigger').classList.remove('active');
        dropdown.querySelector('.dropdown-content').classList.remove('active');
      });
    }
  });

  // Add filter functionality to search
  const searchInput = document.querySelector('.search-input');
  let searchTimeout;
  
  searchInput.addEventListener('input', (e) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      filterItems();
    }, 300);
  });

  // Add filter functionality to checkboxes
  const checkboxes = document.querySelectorAll('.checkbox-wrapper input');
  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', () => {
      updateActiveFilters();
      filterItems();
    });
  });

  // Add filter functionality to tags
  const tags = document.querySelectorAll('.tags-cloud .tag');
  tags.forEach(tag => {
    tag.addEventListener('click', () => {
      tag.classList.toggle('selected');
      updateActiveFilters();
      filterItems();
    });
  });

  // Remove filter tag
  document.addEventListener('click', (e) => {
    if (e.target.closest('.remove-filter')) {
      const filterTag = e.target.closest('.filter-tag');
      const filterText = filterTag.querySelector('span').textContent;
      
      // Remove checkbox selection if it matches
      checkboxes.forEach(checkbox => {
        if (checkbox.nextElementSibling.textContent === filterText) {
          checkbox.checked = false;
        }
      });
      
      // Remove tag selection if it matches
      tags.forEach(tag => {
        if (tag.textContent === filterText) {
          tag.classList.remove('selected');
        }
      });
      
      filterTag.remove();
      filterItems();
    }
  });

  // Reset filters
  const resetButton = document.querySelector('.reset-filters');
  resetButton.addEventListener('click', () => {
    // Clear checkboxes
    checkboxes.forEach(checkbox => checkbox.checked = false);
    
    // Clear selected tags
    tags.forEach(tag => tag.classList.remove('selected'));
    
    // Clear search input
    document.querySelector('.search-input').value = '';
    
    // Clear active filters
    document.querySelector('.active-filters-list').innerHTML = '';
    
    // Reset the view
    filterItems();
  });

  // Helper function to update active filters
  function updateActiveFilters() {
    const activeFiltersList = document.querySelector('.active-filters-list');
    activeFiltersList.innerHTML = '';
    
    // Add selected checkboxes
    checkboxes.forEach(checkbox => {
      if (checkbox.checked) {
        const filterTag = createFilterTag(checkbox.nextElementSibling.textContent);
        activeFiltersList.appendChild(filterTag);
      }
    });
    
    // Add selected tags
    tags.forEach(tag => {
      if (tag.classList.contains('selected')) {
        const filterTag = createFilterTag(tag.textContent);
        activeFiltersList.appendChild(filterTag);
      }
    });
  }

  // Helper function to create filter tags
  function createFilterTag(text) {
    const div = document.createElement('div');
    div.className = 'filter-tag';
    div.innerHTML = `
      <span>${text}</span>
      <button class="remove-filter"><i class="fas fa-times"></i></button>
    `;
    return div;
  }

  // Initial render
  renderItems();
}); 