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
        'background': 'var(--background-color)',
        'background-secondary': 'var(--background-secondary)',
        'text-primary': 'var(--text-primary)',
        'text-secondary': 'var(--text-secondary)',
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