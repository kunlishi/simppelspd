import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import fs from 'fs';

export default defineConfig({
    server: {
        https: {
            key: fs.readFileSync('storage/certs/localhost-key.pem'),
            cert: fs.readFileSync('storage/certs/localhost.pem'),
        },
        host: 'localhost',
    },
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
    build: {
        outDir: "./public/build",
    },
    preview: {
        allowedHosts: ["simppelspd-dev.student.stis.ac.id"],
    },
});
