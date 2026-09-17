export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  safelist: [
    'bg-red',
    'text-red',
    'border-red',
    'hover:bg-red',
    'hover:text-red',
    'hover:bg-red-dark',
    'hover:text-red-dark',
    'focus:border-red',
    'bg-red-dark',
    'text-red-dark',
    'bg-rose',
    'border-rose',
    'bg-green',
    'hover:bg-green',
    'text-green',
    'border-green',
    'bg-purple',
    'text-purple',
    'hover:text-purple',
    'border-purple',
    'bg-yellow',
    'bg-mint',
    'bg-cream',
    'bg-grey',
    'border-grey',
    'bg-light-grey',
    'bg-muted',
    'text-black',
    'shadow-red',
    'shadow-red-sm',
  ],
  theme: {
    extend: {
      colors: {
        red: {
          DEFAULT: 'rgb(var(--color-red) / <alpha-value>)',
          dark: 'rgb(var(--color-red-dark) / <alpha-value>)',
          light: 'rgb(var(--color-red-light) / <alpha-value>)',
          lighter: 'rgb(var(--color-red-lighter) / <alpha-value>)',
        },
        yellow: {
          DEFAULT: 'rgb(var(--color-yellow) / <alpha-value>)',
        },
        purple: {
          DEFAULT: 'rgb(var(--color-purple) / <alpha-value>)',
        },
        green: {
          DEFAULT: 'rgb(var(--color-green) / <alpha-value>)',
        },
        mint: 'rgb(var(--color-mint) / <alpha-value>)',
        black: 'rgb(var(--color-black) / <alpha-value>)',
        grey: 'rgb(var(--color-grey) / <alpha-value>)',
        'light-grey': 'rgb(var(--color-light-grey) / <alpha-value>)',
        muted: 'rgb(var(--color-muted) / <alpha-value>)',
        rose: 'rgb(var(--color-rose) / <alpha-value>)',
        cream: 'rgb(var(--color-cream) / <alpha-value>)',
      },
      fontFamily: {
        sans: ['Switzer', 'system-ui', 'sans-serif'],
        text: ['Manrope', 'system-ui', 'sans-serif'],
      },
      boxShadow: {
        soft: '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
        card: '0 8px 24px rgb(var(--color-black) / 0.08)',
        cta: '0 10px 24px rgb(var(--color-red) / 0.28)',
        'cta-sm': '0 4px 12px rgb(var(--color-red) / 0.25)',
      },
      animation: {
        'fade-in': 'fadeIn 0.5s ease-in-out',
        'slide-up': 'slideUp 0.3s ease-out',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideUp: {
          '0%': { transform: 'translateY(10px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        }
      }
    },
  },
  plugins: [],
}
