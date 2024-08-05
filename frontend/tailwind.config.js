import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
/** @type {import('tailwindcss').Config} */
export default {
  content: ["./index.html", "./src/**/*.{vue,js,ts,jsx,tsx}"],
  theme: {
    extend: {
      fontFamily: {
        sans: ["Figtree", ...defaultTheme.fontFamily.sans],
      },
      colors: {
        morado: {
          light: "#D0CCE6",
          base: "#8b5cf6",
          dark: "#662D91",
        },
        naranjo: {
          "light-0": "#FFD9B5",
          light: "#FF8F59",
          base: "#FF864C",
          dark: "#E75A24",
        },
        gris: {
          light: "#662D91",
          base: "#E5E7EB",
          dark: "#5B6670",
        },
      },
    },
  },

  plugins: [forms],
};
