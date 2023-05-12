/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/**/*.{html,ts}",
  ],
  theme: {
    extend: {
      colors: {
        lightBackGround: '#fffbff',
        transparent: 'transparent',
        current: 'currentColor',
        'white': '#ffffff',
        'slateText': '#1c1b1e',
        'slateBorder': '#7a757f',
        "indigo": {
          "50": "#eef2ff",
          "100": "#e0e7ff",
          "200": "#c7d2fe",
          "300": "#a5b4fc",
          "400": "#818cf8",
          "500": "#6366f1",
          "600": "#4f46e5",
          "700": "#4338ca",
          "800": "#3730a3",
          "900": "#312e81"
        },
      }
    },
  },
  plugins: [],
}

