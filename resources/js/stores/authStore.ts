import {defineStore} from 'pinia'

export const authStore = defineStore('auth', {
  state: () => {
    return {
      id: null,
      email: null,
    }
  }
});
