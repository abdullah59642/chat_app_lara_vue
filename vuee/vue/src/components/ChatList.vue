<template>

<div v-if="displayData != true" class="flex justify-center items-center">
  <div class="animate-spin rounded-full h-7 w-7 border-t-2 mt-20 border-b-2 border-gray-800"></div>
</div>

<div v-if="displayData" class="overflow-y-auto h-full">
<ul>
    <h2 class="my-4 mb-2 ml-2 text-lg text-gray-600">Chats</h2>
    <li>
    <div @click="chatClickEvent(data.id)" v-for="data in chats" :key="data.id">
        <a class="flex py-1 h-20 text-sm border-b border-gray-300 cursor-pointer focus:outline-none">
        <div v-if="userId != data.sender_inverse_relation.id" class="w-full">
                  <div v-if="data?.currentUserReadAt && new Date(data.currentUserReadAt) >= new Date(data.last_time_message)" class="relative flex bg-gray-100 flex-nowrap w-full h-full">
                      <div class="flex items-center shrink-0">
                        <img class="ms-2 h-[70%] rounded-full" :src="data.sender_inverse_relation.image"> 
                      </div>
                      <div class="flex w-full min-w-0">
                        <span class="block font-semibold ms-4 mt-2 text-gray-600  truncate max-w-[180px] sm:max-w-[300px] min-w-0">{{ data.sender_inverse_relation.name }}</span>
                        <span class="block text-sm mt-1 ml-auto me-2 flex-shrink-0">{{formatDate(data.last_time_message)}}</span>
                      </div>
                        <span class="block absolute left-[50%] bottom-0 transform -translate-x-1/2  text-sm py-1 text-gray-600"> {{data.last_message}} </span>
                  </div>
                  <div v-else class="relative flex bg-gray-100 flex-nowrap w-full h-full">
                    <div class="flex items-center shrink-0">
                        <img class="ms-2 h-[70%] rounded-full" :src="data.sender_inverse_relation.image"> 
                      </div>
                      <div class="text-xs absolute right-0 top-1/2 transform -translate-y-1/2">🟢</div>
                      <!-- <span>NOT-READ</span> -->
                      <div class="flex w-full min-w-0">
                        <span class="block font-semibold ms-4 mt-2 text-gray-600  truncate max-w-[180px] sm:max-w-[300px] min-w-0">{{ data.sender_inverse_relation.name }}</span>
                        <span class="block text-sm mt-1 ml-auto me-2 flex-shrink-0">{{formatDate(data.last_time_message)}}</span>
                      </div>
                        <span class="block absolute left-[50%] bottom-0 transform -translate-x-1/2  text-sm py-1 text-gray-600"> {{data.last_message}} </span>
                  </div>
          </div>
          <div v-else class="w-full">
                <div v-if="data?.currentUserReadAt && new Date(data.currentUserReadAt) >= new Date(data.last_time_message)" class="relative flex bg-gray-100 flex-nowrap w-full h-full">
                  <div class="flex items-center shrink-0">
                    <img class="ms-2 h-[70%] rounded-full" :src="data.receiver_inverse_relation.image"> 
                  </div>
                  <div class="flex w-full min-w-0">
                    <span class="block font-semibold ms-4 mt-2 text-gray-600  truncate max-w-[180px] sm:max-w-[300px] min-w-0">{{ data.receiver_inverse_relation.name }}</span>
                    <span class="block text-sm mt-1 ml-auto me-2 flex-shrink-0">{{formatDate(data.last_time_message)}}</span>
                  </div>
                    <span class="block absolute left-[50%] bottom-0 transform -translate-x-1/2  text-sm py-1 text-gray-600"> {{data.last_message}} </span>
                </div>
                <div v-else class="relative flex bg-gray-100 flex-nowrap w-full h-full">
                  <div class="flex items-center shrink-0">
                    <img class="ms-2 h-[70%] rounded-full" :src="data.receiver_inverse_relation.image"> 
                  </div>
                  <div class="text-xs absolute right-0 top-1/2 transform -translate-y-1/2">🟢</div>
                  <div class="flex w-full min-w-0">
                    <span class="block font-semibold ms-4 mt-2 text-gray-600  truncate max-w-[180px] sm:max-w-[300px] min-w-0">{{ data.receiver_inverse_relation.name }}</span>
                    <span class="block text-sm mt-1 ml-auto me-2 flex-shrink-0">{{formatDate(data.last_time_message)}}</span>
                  </div>
                    <span class="block absolute left-[50%] bottom-0 transform -translate-x-1/2  text-sm py-1 text-gray-600"> {{data.last_message}} </span>
                </div>
          </div>
        </a> 
      </div>
    </li>
  </ul>
    <div v-if="chats.length == 0" class="text-center pt-8"> No conversations </div>
</div>
</template>
<script>
  import axios from 'axios'
  import { useMyStore } from '@/store/navbar.js' //here

  export default {
  name: 'ChatList',
  emits: ['chatSelected'],
  data()
  {
      return {
          chatId: 0,
          userStore: useMyStore(),
          chats: [],
          displayData: false,
      }
  },
    props:['refreshChatList', 'userId'], 

    watch: {
      refreshChatList() {
        this.chatRefresh();
      }
    },

    methods:{
      getUserName(chat, userId){
        if(chat.receiver_id == userId){
            return 'receiver';
        } else {
          return 'sender';
        }
      },
    
    async markConversationAsRead()
    {
      let userToken = localStorage.getItem('user-token');
        userToken = userToken.replace(/^"(.*)"$/, '$1');
        await axios.post(`${this.userStore.baseUrl}/api/markChatAsRead`, {
          conversation_id : this.chatId,
          userId: this.userId,
        }, {
            headers:{
                'Authorization': `Bearer ${userToken}`
            }
        });
    },
    
      chatClickEvent(chatId){
        this.chatId = chatId;
        this.$emit('chatSelected', chatId)
        //first update the read state on backend.
        this.markConversationAsRead();
        this.$emit('refreshChatListFromChatBox');
      },
    
      formatDate(dateString) {
        const date = new Date(dateString);
        const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
        return date.toLocaleDateString(undefined, options);  // Localized date format (e.g., "03/30/2026")
      },

      async renderChats(){
        try {
          let userToken = localStorage.getItem('user-token');
          userToken = userToken.replace(/^"(.*)"$/, '$1');
          let result = await axios.get(`${this.userStore.baseUrl}/api/getchats`,{
              headers:{
                  'Authorization': `Bearer ${userToken}`
              }
          });
          if(result.status == 200){
              this.chats = result.data.chats;
              //sort usign updated_at so latest chats appear on top (first)
              this.chats.sort((a, b) => {
                return new Date(b.updated_at) - new Date(a.updated_at);
              });
              this.chats.forEach(chat => {
                let currentUser = this.getUserName(chat, this.userId);
                chat.currentUserReadAt = chat[currentUser + '_read_at'];
              });
          }
          // console.log(this.chats);
          setTimeout(() => {
              this.displayData = true;
            }, 400);
        } catch(error) {
          console.log('something went wrong!');
        }
      },
      
       chatRefresh(){
        this.renderChats();
      },
     },

      async mounted(){
        this.renderChats();
      }
    }
</script>