import { resolve } from 'path';
import { defineConfig } from 'vite';

export default defineConfig({
  build: {
    outDir: resolve(__dirname, 'assets/dist'),
    emptyOutDir: true,
    sourcemap: false,
    minify: true,
    rollupOptions: {
      input: {
        // Admin JavaScript files
        admin: resolve(__dirname, 'assets/src/admin/js/admin.js'),

        // Admin CSS files
        dashboard: resolve(__dirname, 'assets/src/admin/styles/admin.scss'),
        example_feature: resolve(__dirname, 'assets/src/admin/styles/example_feature.scss'),

        // Public JavaScript files
        public: resolve(__dirname, 'assets/src/public/js/public.js'),

        // Public CSS files
        plugin: resolve(__dirname, 'assets/src/public/styles/public.scss'),
      },
      output: {
        // Maintain directory structure for output files
        entryFileNames: (chunkInfo) => {
          const inputFile = chunkInfo.facadeModuleId;
          if (inputFile) {
            if (inputFile.includes('/admin/js/')) {
              return 'admin/js/[name].js';
            } else if (inputFile.includes('/public/js/')) {
              return 'public/js/[name].js';
            }
          }
          return '[name].js';
        },
        chunkFileNames: 'js/[name]-[hash].js',
        assetFileNames: (assetInfo) => {
          if (assetInfo.name && /\.(css|scss)$/.test(assetInfo.name)) {
            // Match the exact filenames referenced in Bootstrap.php
            if (assetInfo.name.includes('dashboard')) {
              return 'admin/styles/dashboard.css';
            } else if (assetInfo.name.includes('example_feature')) {
              return 'admin/styles/example_feature.css';
            } else if (assetInfo.name.includes('plugin')) {
              return 'public/styles/plugin.css';
            }
            return '[name].css';
          }
          return 'assets/[name].[ext]';
        },
      },
    },
  },
});
