<template>
<div class="flex flex-row justify-between">
  <div class="w-1/2 p-4">
    <h1 class="text-2xl font-bold mb-4 mr-8 mt-20">Profile Picture</h1>
    <img :src="picture" alt="IMAGE" class="rounded-full ms-80 w-60 h-60 mb-4">
    <input type="file" @change="handleFileUpload" class="mb-4"><br>
    <button class="bg-green-500 hover:bg-green-700 text-white font-bold mr-10 py-2 px-3 rounded" v-on:click="changePicture()">CHANGE</button>
  </div>
  <div class="w-1/2 p-4 mr-20">
    <h1 class="text-2xl mt-20 font-bold mb-4">Account Details</h1>
    <p class="text-lg">
      <b>Name:</b> {{name}} <br>
      <b>Email:</b> {{email}}
    </p>
  </div>
</div>
  </template>
<script>
import axios from 'axios'
export default{
    name: 'UserAccount',

    data()
    {
        return{
            name: '', 
            email: '',
            picture: '',    
            newImage: null,
        }

    },
  methods:{
    handleFileUpload(event)
      {
        this.newImage = event.target.files[0];
      },
      
  async changePicture()
  {
    if(this.newImage == null)
    {
      alert('no image');
    }
    else
    {
      let userToken = localStorage.getItem('user-token');
      userToken = userToken.replace(/^"(.*)"$/, '$1');
      let formData = new FormData();
      formData.append('image', this.newImage);
     await axios.post("http://127.0.0.1:8000/api/changepicture",formData, 
     {
        headers:{
            'Authorization': `Bearer ${userToken}`,
            'Content-Type': 'multipart/form-data'
        }
     }); 
      this.newImage = '';
     this.refreshApiCall();
    }
  },

 async refreshApiCall()
      {
    let userToken = localStorage.getItem('user-token');
        userToken = userToken.replace(/^"(.*)"$/, '$1');
         const result = await axios.get("http://127.0.0.1:8000/api/accountdata", {
          headers:{
            'Authorization': `Bearer ${userToken}`
          }
        }); 
        this.name = result.data.name;
        this.email = result.data.email;
        this.picture = result.data.image;
      }
  },

     async mounted()
      {
        let userToken = localStorage.getItem('user-token');
        if(!userToken)
        {
          this.$router.push({name:'Home'});
        }
        else
       {
        userToken = userToken.replace(/^"(.*)"$/, '$1');
      const result = await axios.get("http://127.0.0.1:8000/api/accountdata", {
          headers:{
            'Authorization': `Bearer ${userToken}`
          }
        }); 
        this.name = result.data.name;
        this.email = result.data.email;
        this.picture = result.data.image;
      }
   },
}
</script>

<style scoped>
#img{
  width: 190px; 
  height: 200px;
}
</style>