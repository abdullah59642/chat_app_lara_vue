<template>
<nav class="bg-gray-600 px-3 h-10 text-white flex items-center">
    <div class="flex w-full justify-between items-center">
        <router-link to="/" class="px-2 no-underline text-white">ChatAppLaraVue</router-link>
        <div class="flex space-x-2 items-center">
            <router-link to="/login" v-if="!isLoggedIn" class="px-1 no-underline text-white">Login</router-link>
            <router-link to="/signup" v-if="!isLoggedIn" class="px-1 no-underline text-white">Signup</router-link>
        </div>
    <!-- dropdown here -->
      <div v-if="isLoggedIn" class="relative right-0 inline-block text-left px-0">
          <div>
            <button @click="toggleDropdown" type="button" class="inline-flex w-full justify-center gap-x-1.5 rounded-md px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm " id="menu-button" aria-expanded="true" aria-haspopup="true">
              <svg class="-mr-1 h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
          <div v-if="dropdownOpen" class="absolute right-0 z-50 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
            <div class="py-1" role="none">
              <router-link to="/account" @click="closeDropDown" class="text-gray-700 block px-4 py-2 text-m no-underline hover:bg-gray-200" role="menuitem" tabindex="-1" id="menu-item-0">My Account</router-link>
              <div v-on:click="logout" @click="closeDropDown" class="cursor-pointer block hover:bg-red-400 px-4 py-2 text-m no-underline text-red-500" role="menuitem" tabindex="-1" id="menu-item-1">Logout</div>
          </div>
        </div>
      </div>
    <!-- end drop down -->
  </div>
</nav>
</template>

<script>
  import axios from 'axios'
  import { useMyStore } from '@/store/navbar.js'
    export default {
      name: 'NavbarItem',
      data()
      {
        return {
          dropdownOpen: false,
        }
      },
  
    computed: {
      isLoggedIn() {
        const userStore = useMyStore();
        return userStore.loginState;
      }
    },

    created()
    {
      let result = localStorage.getItem('user-token');
        if(result) {
          const userStore = useMyStore();
          userStore.setUserInfo();
        }
    },
 
    methods:{
      toggleDropdown()
      {
        if(this.dropdownOpen == false)
        {
        this.dropdownOpen = true;
        }
        else
        {
        this.dropdownOpen = false;
        }
      
      },

      closeDropDown()
      {
        this.dropdownOpen = false;

      },

      async logout() {
          try {
            let userToken = localStorage.getItem('user-token');     
            userToken = userToken.replace(/^"(.*)"$/, '$1');
            await axios.post("http://127.0.0.1:8000/api/logout", {}, {
              headers: {
                'Authorization': `Bearer ${userToken}`
              }
            });
            // If the request is successful, clear localStorage, update login state, and redirect
            localStorage.clear();
            const userStore = useMyStore();
            userStore.loginState = false;
            window.location.reload();
          } catch (error) {
            console.error('Logout failed:', error);
          }
      },
    }
  }
</script>