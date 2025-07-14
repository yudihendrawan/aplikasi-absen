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
//         origin: "https://ebe99e9007d7.ngrok-free.app/",
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
