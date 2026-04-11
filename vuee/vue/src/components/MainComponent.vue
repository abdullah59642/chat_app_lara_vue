<template>
<div>
  <!-- <div v-if="userStore.loginState"> -->
      <div class="flex h-full border rounded">
        <!-- first div -->
            <div class="w-full lg:w-1/3 border-r border-gray-300 flex flex-col h-full">
                    <div class="mx-6">
                        <div class="relative text-gray-600" ref="dropdown">
                          <input v-model="search" type="text" class="block border-solid w-full mt-2 py-2 pl-10 bg-gray-200 rounded-full outline-none" name="search"
                            placeholder="Search Users"/>
                                  <div v-if="search && users" class="absolute dropdown-menu w-full rounded-xl bg-gray-200 max-h-40 overflow-y-auto z-[9999]">
                                      <div v-for="data in users" :key="data.id" class="flex items-center h-8 justify-between hover:bg-gray-300 rounded-xl p-2">
                                          <img class="h-[26px]">
                                          <span class="ps-2"> {{data.name}} </span>
                                          <button v-on:click="createConversation(data.id)" class="bg-gray-500 hover:bg-gray-700 text-white px-2 me-2 rounded-full">chat</button>
                                      </div>
                                  </div>

                                    <div v-if="search && users.length == 0" class="absolute dropdown-menu w-full rounded-xl bg-gray-200 max-h-40 overflow-y-auto">
                                        <div class="px-3 py-2 border-bottom">
                                            <div class="ml-3">
                                                <span class="text-xs sm:text-sm">No Users found!</span>
                                            </div>
                                        </div>
                                    </div>
                        </div>
                    </div>
              <ChatList :userId="userId" @refreshChatListToFalse="chatListToFalse" @chatSelected="handleChatSelected" :refreshChatList="refreshChatList"/>
            </div>

            <!-- second div  -->
            <div class="hidden lg:flex-1 lg:flex lg:flex-col h-full">
                <ChatBox :userId="userId" @refreshChatList="handleRefreshChatList" :chatId="chatId" :key="chatId"/>
            </div>
            <!-- main div end -->
             <!-- if on mobile show chatbox as overlay on chatlist -->
             <div v-if="chatId !== 0 && isMobile" class="fixed inset-0 z-[9999] bg-white flex flex-col">
                <ChatBox :userId="userId" @refreshChatList="handleRefreshChatList" :chatId="chatId" :key="chatId" @closeMobileChat="chatId = 0"/>
             </div>
        </div>
    <!-- </div> -->
</div>
</template>
     
<script>
import axios from 'axios';
import ChatBox from './ChatBox.vue'
import ChatList from './ChatList.vue'
import { useMyStore } from '@/store/navbar.js' //here
    export default {
      name: 'MainComponent',
      data(){
        return {
          userStore: useMyStore(), //here
          isMobile: window.innerWidth < 1024,
          chatId: 0,
          search: '',
          users: [],
          debounceTimer: null,
          userToken: '',
          refreshChatList: false,
          userId : 0,
        }
      },
      components:{
        ChatBox,
        ChatList,
      },
      watch:{
        search:function(newVal){
          clearTimeout(this.debounceTimer);
          this.debounceTimer = setTimeout(() => {
            this.getUsers(newVal);
          }, 600);
        }
      },

      methods:{
        handleClickOutside(event) {
          const dropdown = this.$refs.dropdown;
            if (dropdown && !dropdown.contains(event.target)) {
              this.search = '';
              this.users = [];
            }
      },

      chatListToFalse()
      {
        this.refreshChatList = false;
      },

      handleRefreshChatList()
      {
        this.refreshChatList = true;
          setTimeout(() => {
          this.refreshChatList = false;
          }, 2000); 
      },

      handleChatSelected(payload){
        this.chatId = payload;
      },

      getUsers(query) {
        axios.post("http://127.0.0.1:8000/api/searchuser", {
          query: query,
        },{
        headers:{
            'Authorization': `Bearer ${this.userToken}`
          }}).then(response => {
          this.users = response.data.users;
          });
      },

      async createConversation(id){
        let result = await axios.post("http://127.0.0.1:8000/api/createconversation", {
          receiver_id: id,
        },{
        headers:{
          'Authorization': `Bearer ${this.userToken}`
          }
        });
          if(result.status == 201){
            this.refreshChatList = true;
            this.search = '';
          }
        },
      },

      async mounted(){
        //check for outside click to hide the dropdown of the users
        document.addEventListener('click', this.handleClickOutside);
        window.addEventListener('resize', () => {
          this.isMobile = window.innerWidth < 1024;
        });

        let userToken = localStorage.getItem('user-token');
        if(!userToken) {
          this.$router.push({name:'SignUp'});
        } else {
          this.userToken = userToken.replace(/^"(.*)"$/, '$1');
          let result = await axios.get("http://127.0.0.1:8000/api/getuserid",{
            headers:{
              'Authorization': `Bearer ${this.userToken}`
            }
          });
          if(result.status == 200)
          {
              this.userId = result.data.id;
          }
         }
        },
      }
</script>

 
            