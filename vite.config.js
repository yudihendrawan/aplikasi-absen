import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: [`resources/views/**/*`],
        }),
        // tailwindcss(),
    ],
    server: {
        cors: true,
    },
});

// ketika mode production jalankan npm run build dan buka script dibawah ini
// origin diganti sesuai dengan url domain
// import { defineConfig } from "vite";
// import laravel from "laravel-vite-plugin";

// export default defineConfig({
//     server: {
//         host: "0.0.0.0",
//         port: 5173,
//         https: true,
//         cors: {
//             origin: "*",
//             methods: ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
//         },
//         origin: "https://731da72e90cd.ngrok-free.app/",
//         strictPort: true,
//     },
//     plugins: [
//         laravel({
//             input: [
//                 "resources/css/app.css",
//                 "resources/js/app.js",
//                 "resources/js/calendar.js",
//                 // "resources/js/plugins.js",
//             ],
//             refresh: true,
//         }),
//     ],
// });
// ketika mode production jalankan npm run build dan buka script diatas ini
