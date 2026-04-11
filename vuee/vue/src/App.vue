<template>
  <div class="h-screen flex flex-col">
    <NavbarItem />
    <div class="flex-1 overflow-hidden p-2 flex">
      <router-view class="flex-1"/>
    </div>
  </div>
</template>

<script>
import NavbarItem from './components/NavbarItem.vue'

export default {
  name: 'App',
  components: {
    NavbarItem,
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
}
/* Scrollbar width */
::-webkit-scrollbar {
  width: 8px; /* thinner than default */
  height: 8px; /* horizontal scrollbar */
}

/* Track (background behind thumb) */
::-webkit-scrollbar-track {
  background: #f0f0f0; /* light gray track */
  border-radius: 4px;
}

/* Scroll thumb (the draggable part) */
::-webkit-scrollbar-thumb {
  background: #c0c0c0; /* gray thumb */
  border-radius: 4px;
  transition: background 0.3s;
}

/* Hover effect */
::-webkit-scrollbar-thumb:hover {
  background: #909090; /* darker on hover */
}
</style>