import { defineStore } from 'pinia'

export const useMyStore = defineStore({
    id: 'user',
    state: () => ({
      loginState: false,
      refreshAdmin: false,
      baseUrl: 'http://127.0.0.1:8000',
    }),
    actions: {
      setUserInfo() {
        this.loginState = true;
      },
      refreshAdmin()
      {
        this.refreshAdmin = true;
      },
    }
  });
