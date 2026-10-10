import vue from '@vitejs/plugin-vue'
import { defineConfig, loadEnv } from 'vite'

// https://vite.dev/config/
export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd())

  return {
    plugins: [vue()],
    server: {
      proxy: {
        '/api': { target: env.VITE_API_TARGET, changeOrigin: true },
        '/sanctum': { target: env.VITE_API_TARGET, changeOrigin: true },
      },
    },
  }
})
