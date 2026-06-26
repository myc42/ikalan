// tailwind.config.js
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/app/**/*.{js,jsx,ts,tsx}",
    "./src/components/**/*.{js,jsx,ts,tsx}",
    "./src/**/*.{js,jsx,ts,tsx}",
  ],
  presets: [require("nativewind/preset")],
  theme: {
    extend: {

      // 🎨 COULEURS
      colors: {
        // Primary
        primary: {
          purple:      "#6C4EF5",  // Lingua Purple
          "deep-purple": "#5B3BF6", // Lingua Deep Purple
          blue:        "#4D8BFF",  // Lingua Blue
          green:       "#21C16B",  // Lingua Green
          DEFAULT:     "#6C4EF5",  // Alias principal → purple
        },

        // Semantic
        success: "#21C16B",
        warning: "#FFC800",
        streak:  "#FF8A00",
        error:   "#FF4D4F",
        info:    "#4D8BFF",

        // Neutrals
        text: {
          primary:   "#0D1328",
          secondary: "#6B7280",
        },
        border:     "#E5E7EB",
        surface:    "#F6F7FB",
        background: "#FFFFFF",
      },

      // ✏️ TYPOGRAPHIE — Police unique : Poppins
      fontFamily: {
        sans: ["Poppins_400Regular", "sans-serif"],
        medium:   ["Poppins_500Medium",   "sans-serif"],
        semibold: ["Poppins_600SemiBold", "sans-serif"],
        bold:     ["Poppins_700Bold",     "sans-serif"],
      },

      fontSize: {
        // Headings
        h1: ["32px", { lineHeight: "38px", fontWeight: "700" }], // Bold, 1.2
        h2: ["24px", { lineHeight: "31px", fontWeight: "600" }], // SemiBold, 1.3
        h3: ["20px", { lineHeight: "26px", fontWeight: "600" }], // SemiBold, 1.3
        h4: ["16px", { lineHeight: "22px", fontWeight: "500" }], // Medium, 1.4
        // Body
        "body-lg": ["16px", { lineHeight: "26px", fontWeight: "400" }], // Regular, 1.6
        "body-md": ["14px", { lineHeight: "22px", fontWeight: "400" }], // Regular, 1.6
        "body-sm": ["13px", { lineHeight: "21px", fontWeight: "400" }], // Regular, 1.6
        caption:   ["11px", { lineHeight: "15px", fontWeight: "400" }], // Regular, 1.4
      },

    },
  },
  plugins: [],
};