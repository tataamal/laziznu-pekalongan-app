import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/developer-dashboard.js",
                "resources/js/ranting-dashboard.js",
                "resources/js/mwc-dashboard.js",
                "resources/js/pc-dashboard.js"
            ],
            refresh: true,
        }),
    ],
});
