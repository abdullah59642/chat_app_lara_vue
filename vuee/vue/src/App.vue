<template>
<NavbarItem/>
  <br>
  <router-view/>
  <!-- <FooterItem/> -->
</template>

<script>
import NavbarItem from './components/NavbarItem.vue'
// import FooterItem from './components/FooterItem.vue'

export default {
  name: 'App',
  components: {
    NavbarItem,
    // FooterItem,
  },
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  /* margin-top: 60px; */
}
</style>