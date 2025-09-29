// vite.config.js
import { defineConfig } from "file:///var/www/html/new_vue_tagxi/node_modules/vite/dist/node/index.js";
import laravel from "file:///var/www/html/new_vue_tagxi/node_modules/laravel-vite-plugin/dist/index.mjs";
import vue from "file:///var/www/html/new_vue_tagxi/node_modules/@vitejs/plugin-vue/dist/index.mjs";
var vite_config_default = defineConfig({
  build: {
    chunkSizeWarningLimit: 4e3,
    outDir: "public/build",
    manifest: true,
    parallel: true,
    sourcemap: false,
    rollupOptions: {
      output: {
        manualChunks(id) {
          if (id.includes("node_modules")) {
            return "vendor";
          }
        }
      }
    }
  },
  plugins: [
    laravel({
      input: "resources/js/app.js",
      refresh: true
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false
        }
      }
    })
  ],
  base: "",
  resolve: {
    alias: {
      "@assets": "/resources/",
      // Update this with the correct path to your images
      "@favicon": "/resources/images/"
      // Update this with the correct path to your images
    }
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCIvdmFyL3d3dy9odG1sL25ld192dWVfdGFneGlcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfZmlsZW5hbWUgPSBcIi92YXIvd3d3L2h0bWwvbmV3X3Z1ZV90YWd4aS92aXRlLmNvbmZpZy5qc1wiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9pbXBvcnRfbWV0YV91cmwgPSBcImZpbGU6Ly8vdmFyL3d3dy9odG1sL25ld192dWVfdGFneGkvdml0ZS5jb25maWcuanNcIjtpbXBvcnQgeyBkZWZpbmVDb25maWcgfSBmcm9tICd2aXRlJztcbmltcG9ydCBsYXJhdmVsIGZyb20gJ2xhcmF2ZWwtdml0ZS1wbHVnaW4nO1xuaW1wb3J0IHZ1ZSBmcm9tICdAdml0ZWpzL3BsdWdpbi12dWUnO1xuXG5leHBvcnQgZGVmYXVsdCBkZWZpbmVDb25maWcoe1xuICAgIGJ1aWxkOiB7XG4gICAgICAgIGNodW5rU2l6ZVdhcm5pbmdMaW1pdDogNDAwMCxcbiAgICAgICAgb3V0RGlyOiAncHVibGljL2J1aWxkJyxcbiAgICAgICAgbWFuaWZlc3Q6IHRydWUsXG4gICAgICAgIHBhcmFsbGVsOiB0cnVlLFxuICAgICAgICBzb3VyY2VtYXA6IGZhbHNlLFxuICAgICAgICByb2xsdXBPcHRpb25zOiB7XG4gICAgICAgIG91dHB1dDoge1xuICAgICAgICAgICAgbWFudWFsQ2h1bmtzKGlkKSB7XG4gICAgICAgICAgICAgICAgaWYgKGlkLmluY2x1ZGVzKCdub2RlX21vZHVsZXMnKSkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4gJ3ZlbmRvcic7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSxcbiAgICAgICAgfSxcbiAgICB9LFxuICAgIH0sXG4gICAgcGx1Z2luczogW1xuICAgICAgICBsYXJhdmVsKHtcbiAgICAgICAgICAgIGlucHV0OiAncmVzb3VyY2VzL2pzL2FwcC5qcycsXG4gICAgICAgICAgICByZWZyZXNoOiB0cnVlLFxuICAgICAgICB9KSxcbiAgICAgICAgdnVlKHtcbiAgICAgICAgICAgIHRlbXBsYXRlOiB7XG4gICAgICAgICAgICAgICAgdHJhbnNmb3JtQXNzZXRVcmxzOiB7XG4gICAgICAgICAgICAgICAgICAgIGJhc2U6IG51bGwsXG4gICAgICAgICAgICAgICAgICAgIGluY2x1ZGVBYnNvbHV0ZTogZmFsc2UsXG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIH0sXG4gICAgICAgIH0pLFxuICAgIF0sXG4gICAgYmFzZTogJycsXG4gICAgcmVzb2x2ZToge1xuICAgICAgICBhbGlhczoge1xuICAgICAgICAgICAgJ0Bhc3NldHMnOiAnL3Jlc291cmNlcy8nLCAvLyBVcGRhdGUgdGhpcyB3aXRoIHRoZSBjb3JyZWN0IHBhdGggdG8geW91ciBpbWFnZXNcbiAgICAgICAgICAgICdAZmF2aWNvbic6ICcvcmVzb3VyY2VzL2ltYWdlcy8nLCAvLyBVcGRhdGUgdGhpcyB3aXRoIHRoZSBjb3JyZWN0IHBhdGggdG8geW91ciBpbWFnZXNcbiAgICAgICAgfSxcbiAgICB9LFxuXG5cbn0pO1xuIl0sCiAgIm1hcHBpbmdzIjogIjtBQUFtUSxTQUFTLG9CQUFvQjtBQUNoUyxPQUFPLGFBQWE7QUFDcEIsT0FBTyxTQUFTO0FBRWhCLElBQU8sc0JBQVEsYUFBYTtBQUFBLEVBQ3hCLE9BQU87QUFBQSxJQUNILHVCQUF1QjtBQUFBLElBQ3ZCLFFBQVE7QUFBQSxJQUNSLFVBQVU7QUFBQSxJQUNWLFVBQVU7QUFBQSxJQUNWLFdBQVc7QUFBQSxJQUNYLGVBQWU7QUFBQSxNQUNmLFFBQVE7QUFBQSxRQUNKLGFBQWEsSUFBSTtBQUNiLGNBQUksR0FBRyxTQUFTLGNBQWMsR0FBRztBQUM3QixtQkFBTztBQUFBLFVBQ1g7QUFBQSxRQUNKO0FBQUEsTUFDSjtBQUFBLElBQ0o7QUFBQSxFQUNBO0FBQUEsRUFDQSxTQUFTO0FBQUEsSUFDTCxRQUFRO0FBQUEsTUFDSixPQUFPO0FBQUEsTUFDUCxTQUFTO0FBQUEsSUFDYixDQUFDO0FBQUEsSUFDRCxJQUFJO0FBQUEsTUFDQSxVQUFVO0FBQUEsUUFDTixvQkFBb0I7QUFBQSxVQUNoQixNQUFNO0FBQUEsVUFDTixpQkFBaUI7QUFBQSxRQUNyQjtBQUFBLE1BQ0o7QUFBQSxJQUNKLENBQUM7QUFBQSxFQUNMO0FBQUEsRUFDQSxNQUFNO0FBQUEsRUFDTixTQUFTO0FBQUEsSUFDTCxPQUFPO0FBQUEsTUFDSCxXQUFXO0FBQUE7QUFBQSxNQUNYLFlBQVk7QUFBQTtBQUFBLElBQ2hCO0FBQUEsRUFDSjtBQUdKLENBQUM7IiwKICAibmFtZXMiOiBbXQp9Cg==
