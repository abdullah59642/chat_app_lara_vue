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
              <div v-if="userId != data.sender_inverse_relation.id" class="relative flex bg-gray-100 flex-nowrap w-full h-full">
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
                  <img class="ms-2 h-[70%] rounded-full" :src="data.receiver_inverse_relation.image"> 
                </div>
                <div class="flex w-full min-w-0">
                  <span class="block font-semibold ms-4 mt-2 text-gray-600  truncate max-w-[180px] sm:max-w-[300px] min-w-0">{{ data.receiver_inverse_relation.name }}</span>
                  <span class="block text-sm mt-1 ml-auto me-2 flex-shrink-0">{{formatDate(data.last_time_message)}}</span>
                </div>
                  <span class="block absolute left-[50%] bottom-0 transform -translate-x-1/2  text-sm py-1 text-gray-600"> {{data.last_message}} </span>
              </div>
        </a> 
      </div>
         <div v-if="chats.length == 0" class="text-center pt-8"> No conversations </div>
    </li>
  </ul>
</div>

</template>
<script>
  import axios from 'axios'
  export default {
  name: 'ChatList',
  emits: ['chatSelected', 'refreshChatListToFalse'],
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
      chatClickEvent(chatId){
        this.$emit('chatSelected', chatId)
      },
    
      formatDate(dateString) {
        const date = new Date(dateString);
        const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
        return date.toLocaleDateString(undefined, options);  // Localized date format (e.g., "03/30/2026")
      },

      async chatRefresh(){
        try {
          let userToken = localStorage.getItem('user-token');
          userToken = userToken.replace(/^"(.*)"$/, '$1');
          let result = await axios.get("http://127.0.0.1:8000/api/getchats",{
              headers:{
                  'Authorization': `Bearer ${userToken}`
              }
          });
          
          if(result.status == 200){
              this.chats = result.data.chats;
              this.$emit('refreshChatListToFalse');
          }
        } catch(error) {
          console.log('something went wrong!');
        }
      },
     },

      async mounted(){
        try {
          let userToken = localStorage.getItem('user-token');
          userToken = userToken.replace(/^"(.*)"$/, '$1');
          const result = await axios.get("http://127.0.0.1:8000/api/getchats",{
              headers:{
                  'Authorization': `Bearer ${userToken}`
              }
          });
          console.log("chats testing");
          console.log(result);

            if(result.status == 200){
              this.chats = result.data.chats;
              //sort usign updated_at so latest chats appear on top (first)
              this.chats.sort((a, b) => {
                return new Date(b.updated_at) - new Date(a.updated_at);
              });
            }
            setTimeout(() => {
              this.displayData = true;
            }, 400);
        } catch(error){ 
          this.$router.push({name:'SignUp'});
        }
      }
    }
</script>