import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],
  build: {
    cssCodeSplit: true,
    lib: {
      entry: 'src/main.js',
      name: 'Voyager 2FA',
      formats: ['umd'],
      fileName: 'voyager-2fa'
    },
    rollupOptions: {
      external: ['vue', 'axios'],
      output: {
        globals: {
          vue: 'Vue',
          axios: 'axios',
        }
      }
    }
  }
})