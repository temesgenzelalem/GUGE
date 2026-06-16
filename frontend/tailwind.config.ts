/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './pages/**/*.{js,ts,jsx,tsx,mdx}',
    './components/**/*.{js,ts,jsx,tsx,mdx}',
    './app/**/*.{js,ts,jsx,tsx,mdx}',
  ],
  theme: {
    extend: {
      colors: {
        ink:      '#1a150e',
        'ink-2':  '#3d3226',
        'ink-3':  '#7a6852',
        paper:    '#faf6ef',
        'paper-2':'#f3ece0',
        'paper-3':'#e8dcc8',
        forest:   '#0d5c43',
        'forest-2':'#1a7a59',
        'forest-3':'#d0ede4',
        amber:    '#b86c14',
        gold:     '#c89a3a',
      },
      fontFamily: {
        serif:   ['Playfair Display', 'Georgia', 'serif'],
        display: ['Syne', 'sans-serif'],
        body:    ['Lora', 'serif'],
      },
      borderRadius: {
        lg: '18px',
        xl: '28px',
      },
    },
  },
  plugins: [],
};
