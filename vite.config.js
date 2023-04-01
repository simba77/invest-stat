import {defineConfig} from 'vite';
import vue from '@vitejs/plugin-vue'
import path from 'node:path'

export default defineConfig({
  resolve:{
    alias:{
      '@' : path.resolve(__dirname, './resources/js')
    },
  },
  plugins: [
    vue(),
  ],
});
