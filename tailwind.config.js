/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                primary: '#0066ff',
                secondary: '#00cc88',
                dark: '#1a1a2e',
            }
        },
    },
    plugins: [],
}
