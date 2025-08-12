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
- Asset enqueuing: `functions.php:27-77`
- ACF customizations: `functions.php:639-1372`

## Testing Approach

The theme doesn't have automated tests. Manual testing should include:
- Cross-browser compatibility
- Responsive design verification
- WordPress admin functionality
- ACF field validation
- Asset compilation verification