<template>

<div v-if="displayData != true" class="flex justify-center items-center">
  <div class="animate-spin rounded-full h-7 w-7 border-t-2 mt-20 border-b-2 border-gray-800"></div>
</div>

<div v-if="displayData">
<ul class="overflow-auto h-[50rem]  ">
    <h2 class="my-4 mb-2 ml-2 text-lg text-gray-600">Chats</h2>
    <li>
    <div @click="chatClickEvent(data.id)" v-for="data in chats" :key="data.id" >
        <a class="flex items-center px-3 py-1 text-sm transition duration-150 ease-in-out border-b border-gray-300 cursor-pointer hover:bg-gray-300 focus:outline-none">
          <div class="w-full">
            <div class="flex justify-between">
              <div v-if="userId != data.sender_inverse_relation.id"  class="flex items-center">
                <img class="h-[48px] mt-2 w-12 rounded-full"  :src="data.sender_inverse_relation.image" alt="username" />
                <span class="block ml-2 font-semibold text-gray-600">{{ data.sender_inverse_relation.name }}</span>
              </div>
              <div v-else  class="flex items-center">
              <img class="h-[48px] mt-2 w-12 rounded-full" :src="data.receiver_inverse_relation.image"> 
              <span class="block ml-2 font-semibold text-gray-600">{{ data.receiver_inverse_relation.name }}</span>
              </div>

              <span class="block ml-2 text-sm text-gray-600">{{data.last_time_message}}</span> 
            </div>
            <span class="block ml-2 text-sm py-1 text-gray-600">  
                        {{data.last_message}}
           </span>
    </div>   
   </a> 
  </div>   
         <div v-if="chats.length == 0" class="text-center text-xl pt-8"> No conversations </div>
    </li>
  </ul>
</div>
   
</template>
<script>
import axios from 'axios'
export default {
name: 'ChatList',
data()
{
    return{
        chats: [],
        displayData: false,
    }
},
  props:['refreshChatList', 'userId'],

  watch:{
    refreshChatList: function(newVal, oldVal)
    {
      if(newVal !== oldVal)
      {
        this.chatRefresh();
      }
    }
  },

  methods:{
    chatClickEvent(chatId)
    {
      this.$emit('chatSelected', chatId)
    },
  
    async chatRefresh()
    {
      try{
        let userToken = localStorage.getItem('user-token');
        userToken = userToken.replace(/^"(.*)"$/, '$1');
        let result = await axios.get("http://127.0.0.1:8000/api/getchats",{
            headers:{
                'Authorization': `Bearer ${userToken}`
            }
        });
        
        if(result.status == 200)
        {
            this.chats = result.data.chats;
            this.$emit('refreshChatListToFalse');
        }
      }
      catch(error)
      {
        console.log('something went wrong!');
      }
    },
    },
    
  
    async mounted()
    {
        try{
        let userToken = localStorage.getItem('user-token');
        userToken = userToken.replace(/^"(.*)"$/, '$1');
        const result = await axios.get("http://127.0.0.1:8000/api/getchats",{
            headers:{
                'Authorization': `Bearer ${userToken}`
            }
        });
        if (result.status == 200)
        {
            this.chats = result.data.chats;
          }
            setTimeout(() => {
          this.displayData = true;
          }, 400);
    }
    catch(error)
    {
        console.log('something went wrong!');
    }
    }
  }
</script>