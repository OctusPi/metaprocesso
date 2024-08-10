import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    vue(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
      'b%': fileURLToPath(new URL('node_modules/bootstrap/dist/css', import.meta.url)),
      'bi%': fileURLToPath(new URL('node_modules/bootstrap-icons/font', import.meta.url))
    }
  }
})
