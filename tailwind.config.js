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
        'primary': 'var(--primary-color)',
        'secondary': 'var(--secondary-color)',
        'accent': 'var(--accent-color)',
        'accent-light': 'var(--accent-color-light)',
        'highlight': 'var(--highlight-color)',
        'initial': 'var(--initial-color)',
      },
      fontFamily: {
        'sans': ['Open Sans', 'sans-serif'],
        'serif': ['Merriweather', 'serif'],
      },
      maxWidth: {
        '50': '50%',
        '80': '80%',
      },
    },
  },
  plugins: [],
} 