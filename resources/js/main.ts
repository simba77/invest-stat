import axios from 'axios';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true

import {createSSRApp} from 'vue'
import {createPinia} from 'pinia'
import router from './router';
import AppComponent from "./App.vue";
import {authStore} from "./stores/authStore";
import FloatingVue from 'floating-vue'
import 'floating-vue/dist/style.css'
import '../sass/app.scss'

export async function createApp() {
  const app = createSSRApp(AppComponent);
  app.use(createPinia())
  app.use(FloatingVue)

  const auth = authStore();
  await auth.checkAuth();

  // Check auth
  router.beforeEach((to) => {
    const auth = authStore();

    console.log('before Each')

    if (to.meta.requiresAuth && !auth.userData) {
      return '/login'
    }

    // Redirect authorized users from guest pages
    if (to.meta.onlyGuests && auth.userData) {
      return '/'
    }
  })

  app.use(router)

  return {
    auth, app, router
  }

// Checking auth and mount app
  /*  auth.checkAuth()
      .finally(() => {
        app.use(router)
        app.mount('#app')
      });*/
}


