const axios = require('axios');
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';


import {createApp} from 'vue'
import {createPinia} from 'pinia'
import router from './router';
import AppComponent from "./App.vue";


const app = createApp(AppComponent)
app.use(createPinia())
app.use(router)
app.mount('#app')
