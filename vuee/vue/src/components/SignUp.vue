
  <template>
    <br>
    <div class="flex justify-center w-full">
    <div class="register flex flex-nowrap flex-col w-[30%] items-center">
      <h1 class="text-4xl font-bold mb-4">SIGN UP</h1>
      <input class="mb-4 p-2  border border-gray-400  rounded" type="text" v-model="name" placeholder="Enter Name"/>
      <input class="mb-4 p-2  border border-gray-400 rounded" type="text" v-model="email" placeholder="Enter Email"/>
      <input class="mb-4 p-2  border border-gray-400 rounded" type="password" v-model="password" placeholder="Enter Password"/>
      <button class="bg-gray-500 flex-nowrap hover:bg-gray-600 text-white py-1 px-2 rounded" @click="signUp()">SIGN UP</button>
    </div>
    </div>
  </template>
  <script>

import axios from 'axios'
import {toast} from 'vue3-toastify'
import { useMyStore } from '@/store/navbar.js'
  export default {
    name: 'SignUp',
    inheritAttrs: false, 
    data() {
      return {
        userStore: useMyStore(),
        name:'',
        email:'',
        password:'',
      }
    },
    methods: {
        async signUp(){
        try {
            let result = await axios.post(`${this.userStore.baseUrl}/api/register`, {
              name: this.name, 
              email: this.email,
              password: this.password,
            });
            if(result.status == 201){
              localStorage.setItem("user-token", JSON.stringify(result.data.token));
              this.userStore.setUserInfo();
              this.$router.push({name:'Main'});
            }
        } catch(error){
            if (error.response) {
              if (error.response.status === 401) {
                this.incorrectCredsToast();
              } else if(error.response.status === 422){
                toast.error('Please check the email format and try again.',{
                autoClose:4000,
              });
              } else {
                toast.error('An unexpected error occurred. Please try again later.',{
                autoClose:4000,
                });
              }
            } else {
              toast.error('Could not reach the server. Please check your connection or try again later.',{
                autoClose:4000,
              });
            }
          }
        }
      },

      mounted(){
        let user = localStorage.getItem('user-token');
        if(user){
          this.$router.push({name:'Main'});
        }
      }
  }
  </script>
  <!-- Add "scoped" attribute to limit CSS to this component only -->
  <style scoped>
  </style>
  