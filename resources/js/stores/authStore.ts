import {defineStore} from 'pinia'
import axios from "axios";
import {config} from "@/config";

export const authStore = defineStore('auth', {
  state: () => {
    return {
      userData: null,
    }
  },
  actions: {
    async checkAuth() {
      return axios.get(config.API_HOST + '/api/checkAuth')
        .then((response) => {
          this.userData = response.data;
        })
        .catch(() => {
          console.log('unauth');
        });
    }
  }
});
