# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is the Ethan Ede WordPress theme - a custom WordPress theme with a focus on portfolio and project showcase. The theme uses a hybrid approach combining traditional WordPress PHP templates with modern build tools for SCSS and Tailwind CSS.

## Build Commands

```bash
# Build all assets (SCSS and Tailwind)
npm run build

# Build SCSS only
npm run scss

# Build Tailwind CSS only
npm run tailwind

# Watch for changes during development
npm run watch
```

## Architecture

### Theme Structure
- **Custom Post Types**: `project` and `deliverable` for portfolio items
- **Custom Taxonomies**: 
  - `project_category` - Main categorization for projects
  - `project_tag` - Tags for projects
  - `deliverable_type` - Types of deliverables
  - `technology` - Tools/technologies used
  - `skill` - Skills demonstrated
  - `company` - Associated companies
- **Template Hierarchy**: Uses WordPress template hierarchy with custom templates in `/templates/` directory
- **Partials**: Reusable components in `/partials/` directory

### Asset Pipeline
- **SCSS**: Source files in `/assets/scss/`, compiled to `/assets/css/main.css` via Gulp
- **Tailwind CSS**: Input from `/assets/css/tailwind.css`, output to `/assets/css/tailwind-output.css`
- **JavaScript**: Modular JS files in `/assets/js/` with specific functionality per file

### Key Integration Points
- **ACF (Advanced Custom Fields)**: Heavy use for custom fields, JSON configs in `/acf-json/`
- **Font Awesome**: Pro icons loaded via CDN
- **Google Fonts**: Roboto and Merriweather loaded with FOUC prevention
- **GSAP (GreenSock)**: JavaScript animation library for card hover effects and arrow animations
- **Performance APIs**: Intersection Observer, requestIdleCallback, Network Information API

## Development Guidelines

### File Naming Conventions
- JavaScript files: kebab-case (e.g., `portfolio-hover.js`)
- SCSS partials: underscore prefix with kebab-case (e.g., `_mobile-menu.scss`)
- PHP templates: WordPress standard naming

### Code Style
- PHP: WordPress coding standards with snake_case functions
- JavaScript: ES6+ with const/let, arrow functions, camelCase variables
- CSS/SCSS: BEM methodology for class naming
- Indentation: 2 spaces for web files, 4 for PHP

### WordPress Specifics
- Always use `wp_enqueue_script()` and `wp_enqueue_style()` for assets
- Sanitize with WordPress functions: `esc_html()`, `esc_attr()`, `wp_kses()`
- Use nonces for security in forms
- Development mode toggle in `functions.php` for cache busting

### Critical Functions Location
- Post type registration: `functions.php:674-1073`
- Taxonomy registration: `functions.php:696-1033`
- Asset enqueuing: `functions.php:27-86`
- ACF customizations: `functions.php:639-1372`
- Performance optimizations: `functions.php:1417-1447`
- Project category admin columns: `functions.php:1453-1504`

### Card System & Animations
- **Master Card System**: `assets/scss/components/_card.scss` - Unified card styling with context variants
- **Card Animations**: `assets/js/portfolio-hover.js` - GSAP-powered hover animations for cards
- **GIF Lazy Loading**: `assets/js/gif-lazy-loader.js` - Performance-optimized GIF loading system
- **Blog Sidebar Styling**: `assets/scss/pages/_blog.scss` - Blog-specific sidebar with gradient borders
- **Animation Dependencies**: GSAP loaded as dependency for portfolio-hover script in `functions.php`

### Blog & Sidebar Implementation
- **Unified Sidebar Design**: Blog sidebars use deliverable sidebar treatment (gradient left border, no background containers)
- **Card Context Variants**: `.card--sidebar` for compact 2-row sidebar cards with full-color images
- **Animation Conflict Resolution**: CSS transforms removed to prevent conflicts with GSAP animations
- **CSS Variables**: 21+ custom properties in `_variables.scss` for consistent theming

## Development Environment

### Local by Flywheel
This site runs on Local by Flywheel. For complete setup instructions, WP-CLI access, and debugging configuration, see [LOCAL-FLYWHEEL-SETUP.md](./LOCAL-FLYWHEEL-SETUP.md).

Key details:
- **Site Path**: `/Users/edede/Local Sites/ethanede/app/public/`
- **WordPress Debug Log**: `/wp-content/debug.log`
- **Database**: local/root/root (check Local app for port)
- **WP-CLI**: Available through Local's shell environment

### Quick WP-CLI Access
```bash
# In VS Code terminal, source Local's environment:
source ~/Library/Application\ Support/Local/ssh-entry/[site-id].sh

# Then run WP-CLI commands:
wp core version
wp term list project_category
```

### GIF Hover Functionality & Performance Optimization
- **Performance-First Design**: Zero impact on Core Web Vitals (TTFB, LCP, FCP)
- **ACF Integration**: `hover_gif` field in project_category taxonomy for GIF uploads
- **Lazy Loading System**: `assets/js/gif-lazy-loader.js` with intersection observer and network awareness
- **Mobile Optimization**: Scroll-triggered and touch-based GIF loading for mobile devices
- **Caching Strategy**: Map-based GIF caching to prevent duplicate downloads
- **Resource Management**: DNS prefetch, requestIdleCallback, and proper observer cleanup
- **Visual Effects**: Grayscale filter with slate blue overlay matching theme aesthetics
- **Admin Interface**: Visual preview columns showing category images, hover GIFs, and display order

### Homepage Design System
- **Transparent Header**: Home header has transparent background (override of standard-header mixin)
- **Portfolio Grid**: Home context cards with special GIF overlay functionality
- **Context-Aware Styling**: Different card behaviors for home vs. archive vs. sidebar contexts
- **Performance Monitoring**: Debug logging and performance tracking for GIF loading (WP_DEBUG mode)

## WordPress Admin Enhancements

### Project Category Management
- **Enhanced Admin Columns**: Visual thumbnails for category images and hover GIFs
- **Display Order Management**: Sortable display order column for homepage portfolio arrangement
- **GIF Preview**: 50x50px GIF thumbnails with "GIF" labels for easy identification
- **Admin Location**: `/wp-admin/edit-tags.php?taxonomy=project_category`
- **Field Validation**: GIF-only file type restriction for hover_gif field
- **Fallback Indicators**: "No image" and "No GIF" placeholders for empty fields

### Performance Considerations
- **Network-Aware Loading**: Only preloads GIFs on 4G+ connections
- **Mobile-First Strategy**: Longer delays and conservative loading on mobile devices
- **Debug Mode**: Comprehensive console logging when WP_DEBUG is enabled
- **Resource Hints**: Automatic DNS prefetch for upload directories on homepage
- **Cache Management**: Prevents duplicate GIF downloads with intelligent caching

## Testing Approach

The theme doesn't have automated tests. Manual testing should include:
- Cross-browser compatibility (especially mobile Safari and Chrome)
- Responsive design verification
- WordPress admin functionality
- ACF field validation
- Asset compilation verification
- Card hover animations (GSAP-powered)
- GIF loading performance and mobile behavior
- Sidebar card display and animations
- Featured image display in blog sidebar cards
- Core Web Vitals monitoring (LCP, FCP, CLS)
- Mobile touch interactions and scroll triggers