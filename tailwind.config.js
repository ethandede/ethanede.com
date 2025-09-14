/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './*.php',
    './templates/**/*.php',
    './partials/**/*.php',
    './assets/js/**/*.js',
    './single-project.php'
  ],
  theme: {
    extend: {
      colors: {
        // Use proper CSS variable names that match _variables.scss
        'primary': 'var(--color-primary)',
        'secondary': 'var(--color-secondary)', 
        'tertiary': 'var(--color-tertiary)',
        'quaternary': 'var(--color-quaternary)',
        'primary-light': 'var(--color-primary-light)',
        'secondary-light': 'var(--color-secondary-light)',
        // Text colors
        'text-primary': 'var(--text-primary)',
        'text-secondary': 'var(--text-secondary)', 
        'text-on-light': 'var(--text-on-light)',
        // Background colors
        'bg-primary': 'var(--bg-primary)',
        'bg-secondary': 'var(--bg-secondary)',
        'bg-card': 'var(--bg-card)',
      },
      fontFamily: {
        // Match the font families from _variables.scss  
        'sans': ['Roboto', 'sans-serif'],    // Updated to match $body-font
        'serif': ['Merriweather', 'serif'],  // Matches $header-font
      },
      maxWidth: {
        '50': '50%',
        '80': '80%',
      },
    },
  },
  plugins: [],
} 