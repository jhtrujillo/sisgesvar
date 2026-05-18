/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./index.html", "./src/**/*.{vue,js,ts,jsx,tsx}"],
  theme: {
    extend: {
      colors: {
        cenicana: {
          DEFAULT: '#009640',
          light: '#10b981',
          dark: '#047857',
          50: '#f0fdf4',
          100: '#dcfce7',
          200: '#bbf7d0',
          300: '#86efac',
          400: '#4ade80',
          500: '#22c55e',
          600: '#16a34a',
          700: '#15803d',
          800: '#009640',
          900: '#14532d',
        },
        brand: {
          gray: '#4B5563',
          lightGray: '#F3F4F6',
          dark: '#1F2937',
        }
      },
      width: {
        128: "32rem"
      },
      boxShadow: {
        'premium': '0 10px 40px -10px rgba(0, 150, 64, 0.08)',
        'premium-hover': '0 20px 50px -10px rgba(0, 150, 64, 0.15)',
      }
    }
  },
  plugins: [require("@tailwindcss/forms")]
};
